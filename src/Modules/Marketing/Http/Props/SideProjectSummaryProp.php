<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Props;

use Foxws\Docs\Models\Project;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Inertia\PropertyContext;
use Inertia\ProvidesInertiaProperty;
use Modules\Marketing\Support\ProjectSource;

/**
 * The homepage "Side projects" list — registered projects whose metadata
 * flags `kind` as `misc`, `personal`, or `other`, listed as rows instead
 * of the main package grid ProjectSummaryProp builds. Some side projects
 * have their own synced docs (e.g. a personal app with a docs/ folder) —
 * those link to their own page; the rest link out to their source.
 */
final class SideProjectSummaryProp implements ProvidesInertiaProperty
{
    /**
     * @param  Collection<int, Project>  $projects
     */
    public function __construct(private readonly Collection $projects) {}

    public function toInertiaProperty(PropertyContext $context): mixed
    {
        return $this->projects->filter(function (Project $project): bool {
            $kind = $project->metadata?->getArrayCopy()['kind'] ?? 'package';

            return $kind !== 'package';
        })->map(function (Project $project): array {
            $metadata = $project->metadata?->getArrayCopy() ?? [];

            $href = $project->versions->isNotEmpty()
                ? '/'.Str::after($project->slug, '/')
                : ProjectSource::url($project, $metadata);

            return [
                'name' => $project->title,
                'slug' => $project->slug,
                'kind' => $metadata['kind'],
                'type' => $metadata['type'] ?? null,
                'desc' => $metadata['desc'] ?? '',
                'status' => $metadata['status'] ?? null,
                'href' => $href,
            ];
        })->values()->all();
    }
}
