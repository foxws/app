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
 * The homepage "Side projects" list. Side projects with synced docs link
 * to their own page; the rest link out to their source.
 */
final class SideProjectSummaryProp implements ProvidesInertiaProperty
{
    /**
     * @param  Collection<int, Project>  $projects
     */
    public function __construct(private readonly Collection $projects) {}

    public function toInertiaProperty(PropertyContext $context): mixed
    {
        return $this->projects->map(function (Project $project): array {
            $metadata = $project->metadata?->getArrayCopy() ?? [];

            return [
                'name' => $project->title,
                'slug' => $project->slug,
                'type' => $metadata['type'] ?? null,
                'desc' => $metadata['desc'] ?? '',
                'status' => $metadata['status'] ?? null,
                'href' => $project->versions->isNotEmpty()
                    ? '/'.Str::after($project->slug, '/')
                    : ProjectSource::url($project, $metadata),
            ];
        })->values()->all();
    }
}
