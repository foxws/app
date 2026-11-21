<?php

use Spatie\Csp\Policy;
use Spatie\Csp\Preset;

function configuredCspDirective(string $name): ?string
{
    $policy = new Policy;

    foreach (config('csp.presets') as $presetClass) {
        /** @var Preset $preset */
        $preset = app($presetClass);
        $preset->configure($policy);
    }

    return collect(explode(';', $policy->getContents()))
        ->map(fn (string $directive) => trim($directive))
        ->first(fn (string $directive) => str_starts_with($directive, "{$name} "));
}

beforeEach(function () {
    config(['app.url' => 'https://app.example.com']);
});

it('allows blob: connections for the video player', function () {
    expect(configuredCspDirective('connect-src'))->toContain('blob:');
});

it('allows images from raw.githubusercontent.com for docs and project images', function () {
    expect(configuredCspDirective('img-src'))->toContain('https://raw.githubusercontent.com');
});

it('allows images from the app subdomains', function () {
    expect(configuredCspDirective('img-src'))->toContain('*.app.example.com');
});
