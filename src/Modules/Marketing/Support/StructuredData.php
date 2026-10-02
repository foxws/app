<?php

declare(strict_types=1);

namespace Modules\Marketing\Support;

use Foxws\Docs\Models\Document;
use Foxws\Docs\Models\Project;
use Foxws\Docs\Models\Version;
use Spatie\SchemaOrg\BreadcrumbList;
use Spatie\SchemaOrg\Graph;
use Spatie\SchemaOrg\Person;
use Spatie\SchemaOrg\Schema;

/**
 * JSON-LD for search engines, printed into app.blade.php as view data.
 * Crawlers always do a full page load, so it never needs to follow
 * Inertia's client-side navigation.
 */
final class StructuredData
{
    public static function home(): Graph
    {
        $graph = new Graph;

        $graph->add(
            Schema::webSite()
                ->name(config('app.name'))
                ->url(route('home'))
                ->author(self::author()),
        );

        $graph->add(self::author());

        return $graph;
    }

    public static function project(Project $project, ?Version $version): Graph
    {
        $metadata = $project->metadata?->getArrayCopy() ?? [];
        $source = ProjectSource::url($project, $metadata);

        $graph = new Graph;

        $graph->add(
            Schema::softwareSourceCode()
                ->name($project->title)
                ->url(url(DocsNavigation::projectPath($project)))
                ->author(self::author())
                ->if(is_string($metadata['desc'] ?? null), fn ($code) => $code->description($metadata['desc']))
                ->if($source !== null, fn ($code) => $code->codeRepository($source))
                ->if(ProjectKind::isPackage($project), fn ($code) => $code->programmingLanguage('PHP'))
                ->if($version !== null, fn ($code) => $code->version($version?->name))
                ->if(is_string($metadata['licence'] ?? null), fn ($code) => $code->license($metadata['licence'])),
        );

        $graph->add(self::breadcrumbs([
            [$project->title, url(DocsNavigation::projectPath($project))],
        ]));

        return $graph;
    }

    public static function document(Project $project, Document $document): BreadcrumbList
    {
        return self::breadcrumbs([
            [$project->title, url(DocsNavigation::projectPath($project))],
            [$document->title, url(DocsNavigation::pathFor($project, $document, null))],
        ]);
    }

    private static function author(): Person
    {
        return Schema::person()
            ->name('François Menning')
            ->url(route('home'))
            ->sameAs([
                'https://github.com/francoism90',
                'https://www.linkedin.com/in/francoismenning/',
            ]);
    }

    /**
     * @param  array<int, array{string, string}>  $trail  Name and URL of each page after the homepage.
     */
    private static function breadcrumbs(array $trail): BreadcrumbList
    {
        $items = collect([[config('app.name'), route('home')], ...$trail])
            ->map(fn (array $page, int $index) => Schema::listItem()
                ->position($index + 1)
                ->name($page[0])
                ->setProperty('item', $page[1]));

        return Schema::breadcrumbList()->itemListElement($items->all());
    }
}
