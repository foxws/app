<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Props;

use Foxws\Docs\Models\Project;
use Inertia\PropertyContext;
use Inertia\ProvidesInertiaProperty;
use Modules\Marketing\Support\DocsNavigation;

/**
 * A project's full page: hero, install command, docs tree, feature tiles.
 */
final class ProjectDetailProp implements ProvidesInertiaProperty
{
    public function __construct(private readonly Project $project) {}

    public function toInertiaProperty(PropertyContext $context): mixed
    {
        $metadata = $this->project->metadata?->getArrayCopy() ?? [];
        $version = $this->project->versions->firstWhere('is_default', true) ?? $this->project->versions->first();
        $documents = $version?->documents()->orderBy('order')->get() ?? collect();

        $nav = DocsNavigation::build($this->project, $documents);

        return [
            'key' => $this->project->slug,
            'name' => $this->project->title,
            'slug' => $metadata['slug'] ?? $this->project->sourceLocation(),
            'flagship' => (bool) ($metadata['flagship'] ?? false),
            'role' => $metadata['role'] ?? null,
            'eyebrow' => $metadata['eyebrow'] ?? '',
            'title_lines' => $metadata['title_lines'] ?? [$this->project->title, ''],
            'lead' => $metadata['lead'] ?? $metadata['desc'] ?? '',
            'install' => $metadata['install'] ?? "composer require {$this->project->sourceLocation()}",
            'meta' => $metadata['meta'] ?? array_filter([
                $version ? ['k' => 'Latest release', 'v' => $version->name] : null,
            ]),
            'nav' => $nav,
            'on_this_page' => $documents->pluck('title')->all(),
            'features' => $metadata['features'] ?? [],
            'code' => $metadata['code'] ?? null,
        ];
    }
}
