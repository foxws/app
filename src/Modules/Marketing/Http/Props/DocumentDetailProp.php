<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Props;

use Foxws\Docs\Models\Document;
use Foxws\Docs\Models\Project;
use Foxws\Docs\Models\Version;
use Illuminate\Support\Collection;
use Inertia\PropertyContext;
use Inertia\ProvidesInertiaProperty;
use Modules\Marketing\Support\DocsNavigation;
use Modules\Marketing\Support\DocumentHeadings;
use Modules\Marketing\Support\DocumentLinks;

/**
 * A single doc's reading page: rendered body, on-this-page headings, and
 * prev/next among its sibling documents.
 */
final class DocumentDetailProp implements ProvidesInertiaProperty
{
    public function __construct(
        private readonly Project $project,
        private readonly Document $document,
        /** @var Collection<int, Document> */
        private readonly Collection $siblings,
        private readonly ?string $version = null,
    ) {}

    public function toInertiaProperty(PropertyContext $context): mixed
    {
        $links = DocumentLinks::build($this->project, $this->siblings, $this->version);

        $rendered = DocumentHeadings::extract($this->document->toHtml(), $this->document->title, $links);

        $overview = $this->project->indexDocument($this->siblings);

        $ordered = DocsNavigation::flatten($this->siblings);
        $index = $ordered->search(fn (Document $d) => $d->is($this->document));

        $prev = $index !== false ? $ordered->get($index - 1) : null;
        $next = $index !== false ? $ordered->get($index + 1) : null;

        return [
            'project' => [
                'name' => $this->project->title,
                'slug' => $this->project->slug,
                'href' => DocsNavigation::projectPath($this->project, $this->version),
                'versions' => $this->project->versions
                    ->map(fn (Version $version): array => ['name' => $version->name, 'is_default' => $version->is_default])
                    ->all(),
                'version' => $this->project->versionOrDefault($this->version)?->name,
            ],
            'title' => $this->document->title,
            'description' => $this->document->resolveSeoDescription(html: $rendered['html']),
            'html' => $rendered['html'],
            'toc' => $rendered['toc'],
            'nav' => DocsNavigation::build($this->project, $this->siblings, $this->version),
            'surround' => [
                $prev ? ['title' => $prev->title, 'path' => DocsNavigation::pathFor($this->project, $prev, $overview, $this->version)] : null,
                $next ? ['title' => $next->title, 'path' => DocsNavigation::pathFor($this->project, $next, $overview, $this->version)] : null,
            ],
        ];
    }
}
