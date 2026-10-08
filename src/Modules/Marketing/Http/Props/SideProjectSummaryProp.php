<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Props;

use Domain\Projects\Models\Project;
use Illuminate\Support\Collection;
use Inertia\PropertyContext;
use Inertia\ProvidesInertiaProperty;

/**
 * The side projects listed on the homepage and under "More projects",
 * each linking to its own project page.
 */
final class SideProjectSummaryProp implements ProvidesInertiaProperty
{
    /**
     * @param  Collection<int, Project>  $projects
     */
    public function __construct(private readonly Collection $projects) {}

    public function toInertiaProperty(PropertyContext $context): mixed
    {
        return $this->projects->map(fn (Project $project): array => [
            'name' => $project->title,
            'slug' => $project->slug,
            'type' => $project->metadataValue('type'),
            'desc' => $project->description(),
            'status' => $project->metadataValue('status'),
            'href' => route('projectShowcase', $project, absolute: false),
        ])->values()->all();
    }
}
