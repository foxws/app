<?php

declare(strict_types=1);

namespace Modules\Marketing\Support;

use Foxws\Docs\Enums\ProjectDriver;
use Foxws\Docs\Models\Project;

/**
 * Where a project's code lives, for an outbound link — the metadata
 * override when set, else a GitHub URL built from the driver's repository
 * slug. Shared by the project detail page and the homepage's package and
 * side-project summaries.
 */
final class ProjectSource
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public static function url(Project $project, array $metadata): ?string
    {
        return match (true) {
            is_string($metadata['source'] ?? null) && $metadata['source'] !== '' => $metadata['source'],
            $project->driver === ProjectDriver::Github => "https://github.com/{$project->sourceLocation()}",
            default => null,
        };
    }
}
