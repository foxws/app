<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Controllers;

use Domain\Projects\Models\Project;
use Foxws\Docs\Models\Document;
use Foxws\ScoutBuilder\ScoutBuilder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Modules\Marketing\Http\Requests\SearchDocsRequest;
use Modules\Marketing\Http\Resources\SearchResultResource;
use Spatie\ResponseCache\Attributes\NoCache;

final class SearchController
{
    #[NoCache]
    public function __invoke(SearchDocsRequest $request): AnonymousResourceCollection
    {
        $search = ScoutBuilder::for(Document::class, $request);
        $project = $request->project();

        if ($project !== null) {
            $search->whereIn('version_id', Project::findBySlug($project)?->versions()->pluck('id')->all() ?? []);
        }

        $documents = $search
            ->query(function ($query): void {
                $query->where('searchable', true)->with(['version.project', 'version.documents:id,version_id,slug']);
            })
            ->take(8)
            ->get();

        return SearchResultResource::collection($documents);
    }
}
