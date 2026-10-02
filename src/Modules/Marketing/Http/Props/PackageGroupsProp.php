<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Props;

use Foxws\Docs\Models\Project;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Inertia\PropertyContext;
use Inertia\ProvidesInertiaProperty;
use Modules\Marketing\Enums\PackageGroup;
use Modules\Marketing\Support\PackagistDownloads;

/**
 * The homepage package grid, split into sections by each package's `group`
 * metadata, in PackageGroup's case order. Packages without a group, or with
 * one PackageGroup doesn't know, close the list unnamed.
 */
final class PackageGroupsProp implements ProvidesInertiaProperty
{
    /**
     * @param  Collection<int, Project>  $projects  Already ordered by title.
     */
    public function __construct(private readonly Collection $projects) {}

    public function toInertiaProperty(PropertyContext $context): mixed
    {
        $byGroup = $this->projects->groupBy(fn (Project $project): string => $this->groupOf($project)?->value ?? '');

        return collect([...PackageGroup::cases(), null])
            ->filter(fn (?PackageGroup $group): bool => $byGroup->has($group->value ?? ''))
            ->map(fn (?PackageGroup $group): array => [
                'name' => $group?->label(),
                'packages' => $byGroup->get($group->value ?? '')
                    ->map(fn (Project $project): array => $this->summarize($project))
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();
    }

    private function groupOf(Project $project): ?PackageGroup
    {
        $group = $project->metadata['group'] ?? null;

        return is_string($group) ? PackageGroup::tryFrom($group) : null;
    }

    /**
     * @return array<string, mixed>
     */
    private function summarize(Project $project): array
    {
        $metadata = $project->metadata?->getArrayCopy() ?? [];

        return [
            'name' => $project->title,
            'slug' => $project->slug,
            'path' => Str::after($project->slug, '/'),
            'role' => $metadata['role'] ?? null,
            'desc' => $metadata['desc'] ?? '',
            'version' => $project->defaultVersion()?->name,
            'downloads' => PackagistDownloads::monthly($project),
        ];
    }
}
