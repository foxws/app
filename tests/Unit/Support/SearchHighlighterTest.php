<?php

use Modules\Marketing\Support\SearchHighlighter;

it('wraps a case-insensitive match in <mark>', function () {
    expect(SearchHighlighter::highlight('Installation Guide', 'install'))
        ->toBe('<mark>Install</mark>ation Guide');
});

it('wraps every occurrence of the query', function () {
    expect(SearchHighlighter::highlight('Docker docker DOCKER', 'docker'))
        ->toBe('<mark>Docker</mark> <mark>docker</mark> <mark>DOCKER</mark>');
});

it('escapes the rest of the text as well as the match', function () {
    expect(SearchHighlighter::highlight('<script>alert(1)</script> install', 'install'))
        ->toBe('&lt;script&gt;alert(1)&lt;/script&gt; <mark>install</mark>');
});

it('returns null when the query does not appear in the text', function () {
    expect(SearchHighlighter::highlight('Configuration', 'install'))->toBeNull();
});

it('returns null for a blank query', function () {
    expect(SearchHighlighter::highlight('Configuration', ''))->toBeNull();
    expect(SearchHighlighter::highlight('Configuration', null))->toBeNull();
});

it('returns null for blank text', function () {
    expect(SearchHighlighter::highlight('', 'install'))->toBeNull();
});

it('treats query characters that are special in regex literally', function () {
    expect(SearchHighlighter::highlight('C++ (Beta)', '(beta)'))
        ->toBe('C++ <mark>(Beta)</mark>');
});
