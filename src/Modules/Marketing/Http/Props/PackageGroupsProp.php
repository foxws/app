<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Props;

use Foxws\Docs\Models\Project;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Inertia\PropertyContext;
use Inertia\ProvidesInertiaProperty;
use Modules\Marketing\Support\PackagistDownloads;

/**
 * The homepage package grid, split into sections by each package's `group`
 * metadata. Known groups come first in GROUP_ORDER, any other group follows
 * alphabetically, and packages without a group close the list unnamed.
 */
final class PackageGroupsProp implements ProvidesInertiaProperty
{
    public const array GROUP_ORDER = ['Deploy & run', 'Search', 'Media', 'Foundations'];

    /**
     * @param  Collection<int, Project>  $projects  Already ordered by title.
     */
    public function __construct(private readonly Collection $projects) {}

    public function toInertiaProperty(PropertyContext $context): mixed
    {
        return $this->projects
            ->groupBy(fn (Project $project): string => $this->groupOf($project))
            ->sortKeysUsing(fn (string $a, string $b): int => [$this->rankOf($a), $a] <=> [$this->rankOf($b), $b])
            ->map(fn (Collection $projects, string $group): array => [
                'name' => $group !== '' ? $group : null,
                'packages' => $projects->map(fn (Project $project): array => $this->summarize($project))->values()->all(),
            ])
            ->values()
            ->all();
    }

    private function groupOf(Project $project): string
    {
        $group = $project->metadata['group'] ?? null;

        return is_string($group) ? trim($group) : '';
    }

    private function rankOf(string $group): int
    {
        if ($group === '') {
            return PHP_INT_MAX;
        }

        $rank = array_search($group, self::GROUP_ORDER, true);

        return $rank === false ? count(self::GROUP_ORDER) : $rank;
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
