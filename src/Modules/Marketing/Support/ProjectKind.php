<?php

declare(strict_types=1);

namespace Modules\Marketing\Support;

use Foxws\Docs\Models\Project;

/**
 * Every project is a package unless its metadata sets `kind` to something
 * else (`misc`, `personal`, `other`), which makes it a side project.
 */
final class ProjectKind
{
    public static function isPackage(Project $project): bool
    {
        return ($project->metadata['kind'] ?? 'package') === 'package';
    }
}
