<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Resources;

use Foxws\Docs\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Modules\Marketing\Support\DocumentHeadings;
use Modules\Marketing\Support\SearchHighlighter;

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
        $body = DocumentHeadings::extract($this->toHtml(), $this->title)['html'];
        $query = $request->query('query');

        $label = $this->title;
        $suffix = $this->section ? "{$project->title} — {$this->section}" : $project->title;
        $description = Str::of($body)->stripTags()->trim()->limit(100)->toString();

        return [
            'label' => $label,
            'labelHtml' => SearchHighlighter::highlight($label, $query),
            'suffix' => $suffix,
            'suffixHtml' => SearchHighlighter::highlight($suffix, $query),
            'prefix' => Str::upper($project->slug),
            'description' => $description,
            'descriptionHtml' => SearchHighlighter::highlight($description, $query),
            'to' => route('document', [$project->slug, $this->slug], absolute: false),
        ];
    }
}
