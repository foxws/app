<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Controllers;

use Foxws\Docs\Models\Document;
use Foxws\Docs\Models\Version;
use Foxws\ScoutBuilder\ScoutBuilder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Laravel\Scout\Builder;
use Modules\Marketing\Http\Requests\SearchDocsRequest;
use Modules\Marketing\Http\Resources\SearchResultResource;
use Spatie\ResponseCache\Attributes\NoCache;

final class SearchController
{
    #[NoCache]
    public function __invoke(SearchDocsRequest $request): AnonymousResourceCollection
    {
        $project = $request->project();

        $documents = ScoutBuilder::for(Document::class, $request)
            ->when($project !== null, fn (Builder $search) => $search->whereIn(
                'version_id',
                Version::query()->whereRelation('project', 'slug', $project)->pluck('id'),
            ))
            ->query(function ($query): void {
                $query->where('searchable', true)->with(['version.project', 'version.documents:id,version_id,slug']);
            })
            ->take(8)
            ->get();

        return SearchResultResource::collection($documents);
    }
}
