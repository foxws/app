---
name: docs-development
description: >
  Configure and apply the foxws/laravel-docs package in Laravel applications.
license: MIT
metadata:
  author: francoism90
---

# Laravel Docs

Use this skill when a Laravel application needs to integrate the `foxws/laravel-docs` package.

## Primary Goal

- pull a package's `docs/*.md` folder from GitHub, at one or more versions, into queryable `Project`/`Version`/`Document` Eloquent models, and let the consuming app render them

## Workflow

### 1. Install and configure

- `composer require foxws/laravel-docs`
- Migrations for `projects`/`versions`/`documents` run automatically — no `vendor:publish` step is required
- Optionally publish `config/docs.php` (`--tag=docs-config`) to set `seo`, `search`, `sync`, and `github.token` defaults

### 2. Register a project and its versions

Registration is a command, not a config array (it doesn't scale as a literal array once many packages are registered):

```bash
php artisan docs:projects:add {slug} {title} --github={owner/repo} \
    --docs-path=docs \
    --seo-title-pattern="%s — Foxws" --seo-description="..."

php artisan docs:versions:add {slug} {name} {ref} --default
```

A project alone has nothing to sync — it needs at least one version. Each version syncs independently from its own `ref` (a git tag or branch, e.g. `v2.0.0`), so a project can have several versions on different commits. Re-running either command with the same `slug`/`name` updates that row's static fields without touching sync bookkeeping (`last_synced_at`/`last_synced_sha`).

You don't have to run `docs:versions:add` at all: with `docs.sync.auto_discover_versions` enabled (the default), `docs:sync` checks each project's latest GitHub release and registers/updates it as the default version automatically. A project with no versions ever registered will pick up its first one this way.

For the common case — a new project tracked from its default branch — pass `--sync` to `docs:projects:add` instead of the two-step `docs:versions:add`/`docs:sync` dance: it syncs just that project immediately, first registering a `latest` version with `ref=main` (marked default) unless `docs.sync.requires_versions` is enabled (the default) for a GitHub project — then only released or explicitly registered versions are synced. Local projects always get `latest`.

Remove a version or a whole project (with its documents, also removed from search) with `docs:versions:remove {project} {name}` and `docs:projects:remove {slug}`. Both ask for confirmation unless `--force` is passed.

### 3. Sync documentation

```bash
php artisan docs:sync
php artisan docs:sync --project={slug} # only that project
```

Syncs every registered version of every registered project, or just one with `--project`. Before syncing, per project: auto-discovers the latest release as the default version (if enabled), then prunes old non-default versions beyond `docs.sync.keep_versions` (default `5`; set to `0` to keep everything; capped at `docs.sync.prune_chunk_size` per run; the default version is never pruned). Schedule it (e.g. `Schedule::command('docs:sync')->daily()` in `routes/console.php`) rather than relying on a webhook — there is none in v1.

Pass `--queue` (or set `docs.sync.queue.enabled` to make it the default; override back to inline with `--sync`) to chain one queued job per project instead of syncing inline — jobs run one at a time, never in parallel, and each project's job is protected against overlapping with another sync of the same project. See [syncing.md](../../../../docs/syncing.md#queued-syncing).

### 4. Query and render

```php
use Foxws\Docs\Models\Project;

$project = Project::where('slug', $slug)->firstOrFail();
$version = $project->versions()->where('is_default', true)->firstOrFail(); // or ->where('name', $requestedVersion)
$document = $version->documents()->where('slug', $slug ?? 'index')->firstOrFail();

$document->resolveSeoTitle();
```

`Document::body` is already-rendered HTML (from markdown). Reach the owning project via `$document->version->project` — there's no direct `project` relation on `Document`. `Document` uses Scout's `Searchable` trait; indexing is gated by `config('docs.search.enabled')` and each document's `searchable` flag (set via front matter) — it stays fully inert with no configured Scout driver when search is disabled.

### 5. Customize the models (optional)

Set `config('docs.models.project')` / `config('docs.models.version')` / `config('docs.models.document')` to your own subclass to add app-specific behavior. Don't set a `$table` or override the relations' keys — the base models already pin those so a differently-named subclass still resolves to the right table/column.

## Rules, References, and Templates

Read before executing:

- `docs/index.md` in this package for the full guide (configuration, registering projects and versions, syncing, models, search)

## Examples

- A blog/docs site pulling each of its own packages' `docs/` folders into one searchable documentation index, with a version switcher in the UI (e.g. "Install → v1.0.0" vs "Install → v2.0.0") backed by `Version`, and the app's own Inertia/Vue pages rendering `Document::body` and using `resolveSeoTitle()` for `<title>`.

## Anti-patterns

- Do not register projects/versions via a config array — use `docs:projects:add` / `docs:versions:add`.
- Do not build a "Page"/site-wide content type on top of this package's current models — that's an intentionally separate, not-yet-built concern.
- Do not disable Scout by removing the dependency — it's a hard dependency by design (see `docs/search.md`); disable indexing via `DOCS_SEARCH_ENABLED=false` instead.
