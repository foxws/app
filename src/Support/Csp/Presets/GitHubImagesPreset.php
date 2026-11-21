<?php

declare(strict_types=1);

namespace Support\Csp\Presets;

use Spatie\Csp\Directive;
use Spatie\Csp\Policy;
use Spatie\Csp\Preset;

/**
 * Allows the images that synced docs and project pages load from the
 * repositories on GitHub.
 */
class GitHubImagesPreset implements Preset
{
    public function configure(Policy $policy): void
    {
        $policy->add(Directive::IMG, 'https://raw.githubusercontent.com');
    }
}
