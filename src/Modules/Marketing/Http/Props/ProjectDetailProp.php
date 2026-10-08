<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Props;

use Domain\Projects\Models\Project;
use Inertia\PropertyContext;
use Inertia\ProvidesInertiaProperty;
use Integrations\Packagist\PackagistDownloads;
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
        $version = $this->project->versionOrDefault($this->requestedVersion);
        $documents = $version?->orderedDocuments() ?? collect();

        $overview = $this->project->indexDocument($documents);
        $navDocuments = $overview ? $documents->reject(fn ($d) => $d->is($overview)) : $documents;

        $versionParam = DocsNavigation::versionParam($version);

        $rendered = $overview
            ? DocumentHeadings::extract($overview->toHtml(), $this->project->title, DocumentLinks::build($this->project, $documents, $versionParam))
            : null;

        $package = array_filter([
            'version' => $version?->name,
            'requires' => $this->project->metadataValue('requires'),
            'laravel' => $this->project->metadataValue('laravel'),
            'runtime' => $this->project->metadataValue('runtime'),
            'licence' => $this->project->metadataValue('licence'),
        ], fn ($value) => $value !== null);

        $packagistName = $this->project->packagistName();

        $firstDocument = DocsNavigation::firstDocument($navDocuments);

        // The overview reads as the first page in the project, so it only
        // ever surrounds forward — into the first real document, same as
        // "get started" — never back.
        $next = $firstDocument ? ['title' => $firstDocument->title, 'path' => DocsNavigation::pathFor($this->project, $firstDocument, $overview, $versionParam)] : null;

        return [
            'key' => $this->project->slug,
            'name' => $this->project->title,
            'slug' => $this->project->metadataValue('slug') ?? $this->project->sourceLocation(),
            'eyebrow' => $this->project->metadataValue('eyebrow') ?? '',
            'lead' => $this->project->metadataValue('lead') ?? $this->project->metadataValue('desc') ?? '',
            // Only packages default to a composer command — a project
            // (an app, a Flatpak remote, ...) shows one only if it sets its own.
            'install' => $this->project->metadataValue('install') ?? ($this->project->isPackage()
                ? "composer require {$this->project->sourceLocation()}"
                : null),
            'overview' => $rendered ? ['html' => $rendered['html'], 'toc' => $rendered['toc']] : null,
            'nav' => DocsNavigation::build($this->project, $documents, $versionParam),
            'versions' => $this->project->versions->map(fn ($v) => ['name' => $v->name, 'is_default' => $v->is_default])->all(),
            'version' => $version?->name,
            'source' => $this->project->sourceUrl(),
            'package' => $package !== [] ? $package : null,
            'downloads' => $packagistName !== null ? PackagistDownloads::monthly($packagistName) : null,
            'get_started' => $firstDocument ? DocsNavigation::pathFor($this->project, $firstDocument, $overview, $versionParam) : null,
            'surround' => [null, $next],
        ];
    }
}
