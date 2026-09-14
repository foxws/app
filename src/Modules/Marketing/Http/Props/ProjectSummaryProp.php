<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Props;

use Foxws\Docs\Models\Project;
use Illuminate\Support\Collection;
use Inertia\PropertyContext;
use Inertia\ProvidesInertiaProperty;

/**
 * The homepage package grid — one summary card per registered project.
 */
final class ProjectSummaryProp implements ProvidesInertiaProperty
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
                'role' => $metadata['role'] ?? null,
                'desc' => $metadata['desc'] ?? '',
                'version' => $project->versions->firstWhere('is_default', true)?->name,
                'flagship' => (bool) ($metadata['flagship'] ?? false),
            ];
        })->values()->all();
    }
}
