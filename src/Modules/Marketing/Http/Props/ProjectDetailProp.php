<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Props;

use Foxws\Docs\Models\Project;
use Inertia\PropertyContext;
use Inertia\ProvidesInertiaProperty;
use Modules\Marketing\Support\DocsNavigation;
use Modules\Marketing\Support\DocumentHeadings;
use Modules\Marketing\Support\DocumentLinks;
use Modules\Marketing\Support\ProjectKind;
use Modules\Marketing\Support\ProjectSource;

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

        $source = ProjectSource::url($this->project, $metadata);

        $firstDocument = DocsNavigation::firstDocument($navDocuments);

        // The overview reads as the first page in the project, so it only
        // ever surrounds forward — into the first real document, same as
        // "get started" — never back.
        $next = $firstDocument ? ['title' => $firstDocument->title, 'path' => DocsNavigation::pathFor($this->project, $firstDocument, $overview, $versionParam)] : null;

        return [
            'key' => $this->project->slug,
            'name' => $this->project->title,
            'slug' => $metadata['slug'] ?? $this->project->sourceLocation(),
            'eyebrow' => $metadata['eyebrow'] ?? '',
            'lead' => $metadata['lead'] ?? $metadata['desc'] ?? '',
            // Only packages default to a composer command — a side project
            // (an app, a Flatpak remote, ...) shows one only if it sets its own.
            'install' => $metadata['install'] ?? (ProjectKind::isPackage($this->project)
                ? "composer require {$this->project->sourceLocation()}"
                : null),
            'overview' => $rendered ? ['html' => $rendered['html'], 'toc' => $rendered['toc']] : null,
            'nav' => DocsNavigation::build($this->project, $documents, $versionParam),
            'versions' => $this->project->versions->map(fn ($v) => ['name' => $v->name, 'is_default' => $v->is_default])->all(),
            'version' => $version?->name,
            'source' => $source,
            'package' => $package !== [] ? $package : null,
            'used_by' => $this->usedBy($metadata),
            'get_started' => $firstDocument ? DocsNavigation::pathFor($this->project, $firstDocument, $overview, $versionParam) : null,
            'surround' => [null, $next],
        ];
    }

    /**
     * `used_by` is a list of projects, or a single project in front matter
     * written before lists were supported (still in published releases).
     * Entries without a name and href are skipped.
     *
     * @param  array<string, mixed>  $metadata
     * @return array<int, array{name: string, href: string, desc?: string}>
     */
    private function usedBy(array $metadata): array
    {
        $usedBy = $metadata['used_by'] ?? null;

        if (! is_array($usedBy)) {
            return [];
        }

        return collect(array_is_list($usedBy) ? $usedBy : [$usedBy])
            ->filter(fn (mixed $entry): bool => is_array($entry) && is_string($entry['name'] ?? null) && is_string($entry['href'] ?? null))
            ->map(fn (array $entry): array => array_filter([
                'name' => $entry['name'],
                'desc' => is_string($entry['desc'] ?? null) ? $entry['desc'] : null,
                'href' => $entry['href'],
            ], fn (?string $value): bool => $value !== null))
            ->values()
            ->all();
    }
}
