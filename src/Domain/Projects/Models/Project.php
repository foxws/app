<?php

declare(strict_types=1);

namespace Domain\Projects\Models;

use Domain\Projects\Enums\PackageGroup;
use Foxws\Docs\Enums\ProjectDriver;
use Foxws\Docs\Models\Project as BaseProject;

/**
 * A laravel-docs project, with the meaning this site gives to its
 * docs/index.md front matter. Registered as `docs.models.project`, so
 * route binding and the package's own relations return this class.
 */
class Project extends BaseProject
{
    public function metadataValue(string $key): mixed
    {
        return $this->metadata[$key] ?? null;
    }

    /**
     * Every project is a package unless its front matter sets `kind` to
     * something else (`misc`, `personal`, `other`), which makes it a side project.
     */
    public function isPackage(): bool
    {
        return ($this->metadataValue('kind') ?? 'package') === 'package';
    }

    public function packageGroup(): ?PackageGroup
    {
        $group = $this->metadataValue('group');

        return is_string($group) ? PackageGroup::tryFrom($group) : null;
    }

    public function description(): string
    {
        $description = $this->metadataValue('desc');

        return is_string($description) ? $description : '';
    }

    /**
     * Where the code lives, for an outbound link: the front matter's
     * `source` when set, else the GitHub repository.
     */
    public function sourceUrl(): ?string
    {
        $source = $this->metadataValue('source');

        if (is_string($source) && $source !== '') {
            return $source;
        }

        return $this->driver === ProjectDriver::Github ? "https://github.com/{$this->sourceLocation()}" : null;
    }

    /**
     * The Composer package name, which is the GitHub repository: the same
     * name the install command uses.
     */
    public function packagistName(): ?string
    {
        if ($this->driver !== ProjectDriver::Github || blank($this->github_repository)) {
            return null;
        }

        return $this->github_repository;
    }
}
