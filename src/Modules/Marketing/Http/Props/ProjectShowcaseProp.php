<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Props;

use Domain\Projects\Models\Project;
use Foxws\Docs\Support\MarkdownDocumentParser;
use Inertia\PropertyContext;
use Inertia\ProvidesInertiaProperty;
use Modules\Marketing\Support\DocsNavigation;

/**
 * A project's own page: hero, introduction and the technologies it
 * is built with. "Read the docs" goes to the front matter's `docs` URL,
 * else to the project's docs on this site when it has any synced.
 */
final class ProjectShowcaseProp implements ProvidesInertiaProperty
{
    /**
     * @param  Project  $project  Loaded with `loadExists('documents')`.
     */
    public function __construct(private readonly Project $project) {}

    public function toInertiaProperty(PropertyContext $context): mixed
    {
        $introduction = $this->project->introduction();

        return [
            'name' => $this->project->title,
            'slug' => $this->project->slug,
            'desc' => $this->project->description(),
            'image' => $this->project->image(),
            'introduction' => $introduction !== null
                ? app(MarkdownDocumentParser::class)->renderAsHtml($introduction)
                : null,
            'technologies' => $this->project->technologies(),
            'docs' => $this->project->docsUrl() ?? ($this->project->getAttribute('documents_exists')
                ? DocsNavigation::projectPath($this->project)
                : null),
            'source' => $this->project->sourceUrl(),
        ];
    }
}
