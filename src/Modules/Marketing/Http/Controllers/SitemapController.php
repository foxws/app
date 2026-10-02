<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Controllers;

use Domain\Projects\Models\Project;
use Foxws\Docs\Models\Document;
use Modules\Marketing\Support\DocsNavigation;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

final class SitemapController
{
    public function __invoke(): Sitemap
    {
        $sitemap = Sitemap::create()
            ->add(Url::create(route('home')))
            ->add(Url::create(route('terms')));

        Project::with('versions')
            ->orderBy('title')
            ->get()
            ->each(fn (Project $project) => $this->addProject($sitemap, $project));

        return $sitemap;
    }

    /**
     * Only the default version is listed: other versions are the same pages
     * behind a `?version=` query string, which would read as duplicates.
     */
    private function addProject(Sitemap $sitemap, Project $project): void
    {
        $documents = $project->defaultVersion()?->orderedDocuments() ?? collect();

        if ($documents->isEmpty()) {
            return;
        }

        $overview = $project->indexDocument($documents);

        $sitemap->add(
            Url::create(url(DocsNavigation::projectPath($project)))
                ->setLastModificationDate($documents->max('updated_at')),
        );

        $documents
            ->reject(fn (Document $document): bool => (bool) $overview?->is($document))
            ->each(fn (Document $document) => $sitemap->add(
                Url::create(url(DocsNavigation::pathFor($project, $document, $overview)))
                    ->setLastModificationDate($document->updated_at),
            ));
    }
}
