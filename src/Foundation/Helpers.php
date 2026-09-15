<?php

declare(strict_types=1);

use Illuminate\Support\Str;

if (! function_exists('markdown')) {
    function markdown(?string $value = null): string
    {
        if (blank($value)) {
            return '';
        }

        return Str::markdown($value, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }
}
