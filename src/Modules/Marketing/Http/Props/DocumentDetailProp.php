<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Props;

use Foxws\Docs\Models\Document;
use Foxws\Docs\Models\Project;
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
    ) {}

    public function toInertiaProperty(PropertyContext $context): mixed
    {
        $links = DocumentLinks::build($this->project, $this->siblings);

        $rendered = DocumentHeadings::extract($this->document->toHtml(), $this->document->title, $links);

        $index = $this->siblings->search(fn (Document $d) => $d->is($this->document));

        $prev = $index !== false ? $this->siblings->get($index - 1) : null;
        $next = $index !== false ? $this->siblings->get($index + 1) : null;

        return [
            'project' => [
                'name' => $this->project->title,
                'slug' => $this->project->slug,
            ],
            'title' => $this->document->title,
            'html' => $rendered['html'],
            'toc' => $rendered['toc'],
            'nav' => DocsNavigation::build($this->project, $this->siblings),
            'surround' => [
                $prev ? ['title' => $prev->title, 'path' => route('document', [$this->project->slug, $prev->slug], absolute: false)] : null,
                $next ? ['title' => $next->title, 'path' => route('document', [$this->project->slug, $next->slug], absolute: false)] : null,
            ],
        ];
    }
}
