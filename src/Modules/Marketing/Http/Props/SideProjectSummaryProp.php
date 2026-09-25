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
 * to their own page; the rest link out to their source. A version alone
 * isn't enough — auto-discovery registers one for any GitHub release,
 * even when the repository has no docs folder to fill it.
 */
final class SideProjectSummaryProp implements ProvidesInertiaProperty
{
    /**
     * @param  Collection<int, Project>  $projects  Loaded with `withExists('documents')`.
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
                'href' => $project->getAttribute('documents_exists')
                    ? '/'.Str::after($project->slug, '/')
                    : ProjectSource::url($project, $metadata),
            ];
        })->values()->all();
    }
}
