<?php

declare(strict_types=1);

namespace Domain\Docs\Models;

use Foxws\Docs\Enums\ProjectDriver;
use Foxws\Docs\Models\Project as BaseProject;

class Project extends BaseProject
{
    /**
     * Where this project's source code is browsed, per its metadata and
     * driver — a metadata `source` override for a project hosted somewhere
     * other than GitHub, or the GitHub URL derived from sourceLocation()
     * for a Github-driven project. Null when neither applies (e.g. a
     * Local-driven project with no override).
     */
    public function sourceUrl(): ?string
    {
        $metadata = $this->metadata?->getArrayCopy() ?? [];

        return match (true) {
            is_string($metadata['source'] ?? null) && $metadata['source'] !== '' => $metadata['source'],
            $this->driver === ProjectDriver::Github => "https://github.com/{$this->sourceLocation()}",
            default => null,
        };
    }
}
