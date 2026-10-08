<?php

declare(strict_types=1);

namespace Modules\Marketing\Http\Props;

use Domain\Projects\Models\Project;
use Foxws\Docs\Support\MarkdownDocumentParser;
use Inertia\PropertyContext;
use Inertia\ProvidesInertiaProperty;
use Modules\Marketing\Support\DocsNavigation;
use Modules\Marketing\Support\ReadmeIntroduction;

/**
 * A project's own page: hero, introduction and the technologies it
 * is built with. The introduction is the front matter's own, else the
 * one in the README that docs:sync stored. "Read the docs" goes to the front
 * matter's `docs` URL, else to the project's docs on this site when it
 * has any synced.
 */
final class ProjectShowcaseProp implements ProvidesInertiaProperty
{
    /**
     * @param  Project  $project  Loaded with `loadExists('documents')`.
     */
    public function __construct(private readonly Project $project) {}

    public function toInertiaProperty(PropertyContext $context): mixed
    {
        $introduction = $this->project->introduction() ?? $this->readmeIntroduction();

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

    private function readmeIntroduction(): ?string
    {
        $repository = $this->project->githubRepository();
        $readme = $this->project->file('README.md');

        if ($repository === null || $readme === null) {
            return null;
        }

        return ReadmeIntroduction::extract($readme->body, $repository);
    }
}
