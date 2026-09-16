<?php

use Illuminate\Support\Str;
use Modules\Marketing\Support\DocumentDescription;

it('prefers the document\'s own seo description', function () {
    $result = DocumentDescription::resolve('Document description', 'Project description', '<p>Body text.</p>');

    expect($result)->toBe('Document description');
});

it('falls back to the project\'s seo description', function () {
    $result = DocumentDescription::resolve(null, 'Project description', '<p>Body text.</p>');

    expect($result)->toBe('Project description');
});

it('treats a blank seo description as unset and falls through', function () {
    $result = DocumentDescription::resolve('   ', '', '<p>Body text.</p>');

    expect($result)->toBe('Body text.');
});

it('falls back to an excerpt of the rendered body', function () {
    $result = DocumentDescription::resolve(null, null, '<p>Install the package, then publish the config file.</p>');

    expect($result)->toBe('Install the package, then publish the config file.');
});

it('strips tags and collapses whitespace in the excerpt', function () {
    $html = "<h2>Usage</h2>\n<p>Add the   trait to your\nmodel.</p>";

    $result = DocumentDescription::resolve(null, null, $html);

    expect($result)->toBe('Usage Add the trait to your model.');
});

it('truncates a long excerpt to 160 characters', function () {
    $html = '<p>'.str_repeat('word ', 60).'</p>';

    $result = DocumentDescription::resolve(null, null, $html);

    expect($result)->toEndWith('...')
        ->and(mb_strlen(Str::before($result, '...')))->toBeLessThanOrEqual(160);
});
