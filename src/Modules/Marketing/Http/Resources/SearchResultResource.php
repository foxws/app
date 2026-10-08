<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Resources;

use Foxws\Docs\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
use Modules\Marketing\Support\DocsNavigation;
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
        $version = $this->version;
        $project = $version->project;
        $body = DocumentHeadings::extract($this->toHtml(), $this->title)['html'];
        $query = $request->query('query');

        $label = $this->title;
        $suffix = $this->section ? "{$project->title} — {$this->section}" : $project->title;
        $description = Str::of(html_entity_decode(strip_tags($body), ENT_QUOTES | ENT_HTML5, 'UTF-8'))->trim()->limit(100)->toString();

        return [
            'label' => $label,
            'labelHtml' => SearchHighlighter::highlight($label, $query),
            'suffix' => $suffix,
            'suffixHtml' => SearchHighlighter::highlight($suffix, $query),
            'prefix' => Str::upper($project->slug),
            'project' => $project->title,
            'section' => $this->section,
            'version' => $version->name,
            'description' => $description,
            'descriptionHtml' => SearchHighlighter::highlight($description, $query),
            'to' => DocsNavigation::pathFor(
                $project,
                $this->resource,
                $project->indexDocument($version->documents),
                DocsNavigation::versionParam($version),
            ),
        ];
    }
}
