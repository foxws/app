<?php

use Spatie\Csp\Policy;
use Support\Csp\Presets\BasicPreset;

it('allows blob: connections for the video player', function () {
    config([
        'app.url' => 'https://app.example.com',
    ]);

    $policy = new Policy;
    (new BasicPreset)->configure($policy);

    $contents = $policy->getContents();

    $connectDirective = collect(explode(';', $contents))
        ->first(fn (string $directive) => str_starts_with(trim($directive), 'connect-src'));

    expect($connectDirective)->toContain('blob:');
});

it('allows images from raw.githubusercontent.com for docs and project images', function () {
    config([
        'app.url' => 'https://app.example.com',
    ]);

    $policy = new Policy;
    (new BasicPreset)->configure($policy);

    $contents = $policy->getContents();

    $imgDirective = collect(explode(';', $contents))
        ->first(fn (string $directive) => str_starts_with(trim($directive), 'img-src'));

    expect($imgDirective)->toContain('https://raw.githubusercontent.com');
});
