<?php

declare(strict_types=1);

namespace Domain\Tags\Collections;

use Domain\Tags\Models\Tag;
use Illuminate\Database\Eloquent\Collection;

class TagCollection extends Collection
{
    public function translated(): mixed
    {
        return $this
            ->map(fn (Tag $item) => [
                'name' => $item->getTranslations('name'),
                'description' => $item->getTranslations('description'),
            ])
            ->flatten()
            ->filter()
            ->unique()
            ->values();
    }
}
