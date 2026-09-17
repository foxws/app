<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Props;

use Foxws\Docs\Enums\ProjectDriver;
use Foxws\Docs\Models\Project;
use Inertia\PropertyContext;
use Inertia\ProvidesInertiaProperty;
use Modules\Marketing\Support\DocsNavigation;
use Modules\Marketing\Support\DocumentHeadings;
use Modules\Marketing\Support\DocumentLinks;

/**
 * A project's full page: hero, install command, docs tree, and the
 * project's overview document (docs/index.md, or docs/about.md when no
 * index.md exists) rendered as the page body. The overview stays in the
 * docs tree alongside its siblings — same as on every other document page —
 * so the tree's shape doesn't shift depending on which page you're viewing.
 */
final class ProjectDetailProp implements ProvidesInertiaProperty
{
    public function __construct(
        private readonly Project $project,
        private readonly ?string $requestedVersion = null,
    ) {}

    public function toInertiaProperty(PropertyContext $context): mixed
    {
        $metadata = $this->project->metadata?->getArrayCopy() ?? [];
        $version = $this->project->versionOrDefault($this->requestedVersion);
        $documents = $version?->orderedDocuments() ?? collect();

        $overview = $this->project->indexDocument($documents);
        $navDocuments = $overview ? $documents->reject(fn ($d) => $d->is($overview)) : $documents;

        // Only stamp generated links with ?version= when browsing something
        // other than the default — keeps the common case's URLs clean.
        $versionParam = $version && ! $version->is_default ? $version->name : null;

        $rendered = $overview
            ? DocumentHeadings::extract($overview->toHtml(), $this->project->title, DocumentLinks::build($this->project, $documents, $versionParam))
            : null;

        $package = array_filter([
            'version' => $version?->name,
            'requires' => $metadata['requires'] ?? null,
            'laravel' => $metadata['laravel'] ?? null,
            'runtime' => $metadata['runtime'] ?? null,
            'licence' => $metadata['licence'] ?? null,
        ], fn ($value) => $value !== null);

        $source = match (true) {
            is_string($metadata['source'] ?? null) && $metadata['source'] !== '' => $metadata['source'],
            $this->project->driver === ProjectDriver::Github => "https://github.com/{$this->project->sourceLocation()}",
            default => null,
        };

        $usedBy = is_array($metadata['used_by'] ?? null) ? array_filter([
            'name' => $metadata['used_by']['name'] ?? null,
            'desc' => $metadata['used_by']['desc'] ?? null,
            'href' => $metadata['used_by']['href'] ?? null,
        ], fn ($value) => $value !== null) : [];

        $firstDocument = DocsNavigation::firstDocument($navDocuments);

        return [
            'key' => $this->project->slug,
            'name' => $this->project->title,
            'slug' => $metadata['slug'] ?? $this->project->sourceLocation(),
            'eyebrow' => $metadata['eyebrow'] ?? '',
            'lead' => $metadata['lead'] ?? $metadata['desc'] ?? '',
            'install' => $metadata['install'] ?? "composer require {$this->project->sourceLocation()}",
            'overview' => $rendered ? ['html' => $rendered['html'], 'toc' => $rendered['toc']] : null,
            'nav' => DocsNavigation::build($this->project, $documents, $versionParam),
            'versions' => $this->project->versions->map(fn ($v) => ['name' => $v->name, 'is_default' => $v->is_default])->all(),
            'version' => $version?->name,
            'source' => $source,
            'package' => $package !== [] ? $package : null,
            'used_by' => isset($usedBy['name'], $usedBy['href']) ? $usedBy : null,
            'get_started' => $firstDocument ? DocsNavigation::pathFor($this->project, $firstDocument, $overview, $versionParam) : null,
        ];
    }
}
