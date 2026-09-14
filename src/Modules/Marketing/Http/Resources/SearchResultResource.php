<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Resources;

use Foxws\Docs\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/**
 * A single search hit, shaped for the command palette's item slot.
 *
 * @mixin Document
 */
final class SearchResultResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $project = $this->version->project;

        return [
            'label' => $this->title,
            'suffix' => $this->section ? "{$project->title} — {$this->section}" : $project->title,
            'prefix' => Str::upper($project->slug),
            'to' => route('document', [$project->slug, $this->slug]),
        ];
    }
}
