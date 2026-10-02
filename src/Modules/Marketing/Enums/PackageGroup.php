<?php

declare(strict_types=1);

namespace Modules\Marketing\Enums;

use Domain\Shared\Contracts\Enumerable;

/**
 * The homepage package sections, in the order they're shown. A package
 * picks one with `group: <value>` in its docs/index.md front matter.
 */
enum PackageGroup: string implements Enumerable
{
    case Deploy = 'deploy';
    case Search = 'search';
    case Media = 'media';
    case Foundations = 'foundations';

    public function label(): string
    {
        return self::labels()[$this->value];
    }

    /** @return array<string, string> */
    public static function labels(): array
    {
        return [
            'deploy' => __('Deploy & run'),
            'search' => __('Search'),
            'media' => __('Media'),
            'foundations' => __('Foundations'),
        ];
    }
}
