<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Controllers;

use Domain\Projects\Models\Project;
use Foxws\Docs\Models\Document;
use Foxws\ScoutBuilder\AllowedFilter;
use Foxws\ScoutBuilder\ScoutBuilder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Laravel\Scout\Builder;
use Modules\Marketing\Http\Resources\SearchResultResource;
use Spatie\ResponseCache\Attributes\NoCache;

final class SearchController
{
    #[NoCache]
    public function __invoke(Request $request): AnonymousResourceCollection
    {
        $request->validate([
            'query' => ['required', 'string', 'min:2', 'max:100'],
            'filter.project' => ['nullable', 'string'],
        ]);

        $documents = ScoutBuilder::for(Document::class, $request)
            ->allowedFilters(
                AllowedFilter::callback('project', function (Builder $query, mixed $value): void {
                    $versionIds = Project::findBySlug($value)?->versions()->pluck('id') ?? [];

                    $query->whereIn('version_id', $versionIds);
                }),
            )
            ->query(function ($query): void {
                $query->where('searchable', true)->with(['version.project', 'version.documents:id,version_id,slug']);
            })
            ->take(8)
            ->get();

        return SearchResultResource::collection($documents);
    }
}
