<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Props;

use Domain\Projects\Enums\PackageGroup;
use Domain\Projects\Models\Project;
use Illuminate\Support\Collection;
use Inertia\PropertyContext;
use Inertia\ProvidesInertiaProperty;
use Integrations\Packagist\PackagistDownloads;
use Modules\Marketing\Support\DocsNavigation;

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
        $byGroup = $this->projects->groupBy(fn (Project $project): string => $project->packageGroup()->value ?? '');

        return collect([...PackageGroup::cases(), null])
            ->filter(fn (?PackageGroup $group): bool => $byGroup->has($group->value ?? ''))
            ->map(fn (?PackageGroup $group): array => [
                'name' => $group?->label(),
                'packages' => $byGroup->get($group->value ?? '', collect())
                    ->map(fn (Project $project): array => $this->summarize($project))
                    ->values()
                    ->all(),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function summarize(Project $project): array
    {
        $package = $project->packagistName();

        return [
            'name' => $project->title,
            'slug' => $project->slug,
            'href' => DocsNavigation::projectPath($project),
            'role' => $project->metadataValue('role'),
            'desc' => $project->description(),
            'version' => $project->defaultVersion()?->name,
            'downloads' => $package !== null ? PackagistDownloads::monthly($package) : null,
        ];
    }
}
