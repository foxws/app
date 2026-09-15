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
 * index.md exists) rendered as the page body — the docs tree's other
 * entries become sibling pages, not this one.
 */
final class ProjectDetailProp implements ProvidesInertiaProperty
{
    public function __construct(private readonly Project $project) {}

    public function toInertiaProperty(PropertyContext $context): mixed
    {
        $metadata = $this->project->metadata?->getArrayCopy() ?? [];
        $version = $this->project->versions->firstWhere('is_default', true) ?? $this->project->versions->first();
        $documents = $version?->documents()->orderBy('order')->get() ?? collect();

        $overview = $documents->firstWhere('slug', 'index') ?? $documents->firstWhere('slug', 'about');
        $navDocuments = $overview ? $documents->reject(fn ($d) => $d->is($overview)) : $documents;

        $rendered = $overview
            ? DocumentHeadings::extract($overview->toHtml(), $this->project->title, DocumentLinks::build($this->project, $documents))
            : null;

        $package = array_filter([
            'version' => $version?->name,
            'requires' => $metadata['requires'] ?? null,
            'laravel' => $metadata['laravel'] ?? null,
            'runtime' => $metadata['runtime'] ?? null,
            'licence' => $metadata['licence'] ?? null,
        ], fn ($value) => $value !== null);

        $usedBy = is_array($metadata['used_by'] ?? null) ? array_filter([
            'name' => $metadata['used_by']['name'] ?? null,
            'desc' => $metadata['used_by']['desc'] ?? null,
            'href' => $metadata['used_by']['href'] ?? null,
        ], fn ($value) => $value !== null) : [];

        $firstDocument = DocsNavigation::flatten($navDocuments)->first();

        return [
            'key' => $this->project->slug,
            'name' => $this->project->title,
            'slug' => $metadata['slug'] ?? $this->project->sourceLocation(),
            'eyebrow' => $metadata['eyebrow'] ?? '',
            'lead' => $metadata['lead'] ?? $metadata['desc'] ?? '',
            'install' => $metadata['install'] ?? "composer require {$this->project->sourceLocation()}",
            'overview' => $rendered ? ['html' => $rendered['html'], 'toc' => $rendered['toc']] : null,
            'nav' => DocsNavigation::build($this->project, $navDocuments),
            'versions' => $this->project->versions->map(fn ($v) => ['name' => $v->name, 'is_default' => $v->is_default])->all(),
            'github' => $this->project->driver === ProjectDriver::Github ? $this->project->sourceLocation() : null,
            'package' => $package !== [] ? $package : null,
            'used_by' => isset($usedBy['name'], $usedBy['href']) ? $usedBy : null,
            'get_started' => $firstDocument ? route('document', [$this->project->slug, $firstDocument->slug], absolute: false) : null,
        ];
    }
}
