<?php

use Modules\Marketing\Support\DocumentHeadings;

it('assigns ids and nests h3s under the h2 that precedes them, including the first one', function () {
    $html = '<h2>Setup</h2><p>...</p><h3>Step one</h3><h3>Step two</h3><h2>Advanced</h2><h3>Tuning</h3>';

    $result = DocumentHeadings::extract($html);

    expect($result['toc'])->toBe([
        [
            'id' => 'setup',
            'text' => 'Setup',
            'children' => [
                ['id' => 'step-one', 'text' => 'Step one'],
                ['id' => 'step-two', 'text' => 'Step two'],
            ],
        ],
        [
            'id' => 'advanced',
            'text' => 'Advanced',
            'children' => [
                ['id' => 'tuning', 'text' => 'Tuning'],
            ],
        ],
    ]);

    expect($result['html'])
        ->toContain('<h2 id="setup">Setup</h2>')
        ->toContain('<h3 id="step-one">Step one</h3>');
});

it('dedupes ids for headings with the same text', function () {
    $result = DocumentHeadings::extract('<h2>Usage</h2><h2>Usage</h2>');

    expect(array_column($result['toc'], 'id'))->toBe(['usage', 'usage-1']);
});

it('strips a leading h1 that matches the document title', function () {
    $result = DocumentHeadings::extract('<h1>Installation</h1><p>Run this.</p>', 'Installation');

    expect($result['html'])
        ->not->toContain('<h1')
        ->toContain('<p>Run this.</p>');
});

it('keeps a leading h1 that does not match the document title', function () {
    $result = DocumentHeadings::extract('<h1>Something else</h1><p>Run this.</p>', 'Installation');

    expect($result['html'])->toContain('<h1>Something else</h1>');
});

it('rewrites cross-reference links found in the given map and leaves others untouched', function () {
    $html = '<p>See <a href="installation.md">install</a> or <a href="https://example.com">this</a>.</p>';

    $result = DocumentHeadings::extract($html, links: [
        'installation.md' => 'https://example.test/docs/installation',
    ]);

    expect($result['html'])
        ->toContain('href="https://example.test/docs/installation"')
        ->toContain('href="https://example.com"');
});

it('returns empty output for empty markup', function () {
    expect(DocumentHeadings::extract(''))->toBe(['html' => '', 'toc' => []]);
});
