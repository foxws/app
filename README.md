# foxws.nl

The source of [foxws.nl](https://foxws.nl): the homepage and documentation site for the Foxws Laravel packages.

It's shared as a working example of those packages running together in a real application. It's provided as-is, for reading and borrowing from. It isn't a starter kit, and other setups aren't supported.

## Stack

- Laravel 13 on [Octane](https://laravel.com/docs/octane) (FrankenPHP), PostgreSQL and Valkey
- [Inertia v3](https://inertiajs.com) with Vue 3 and SSR, styled with [Nuxt UI](https://ui.nuxt.com) and Tailwind CSS 4
- [Wayfinder](https://github.com/laravel/wayfinder) for typed routes, [Pest](https://pestphp.com) for tests, PHPStan level 8
- Hosted on [Laravel Cloud](https://cloud.laravel.com)

## Foxws packages in use

| Package                                                                       | Where to look                                                                                                                                                     |
| ----------------------------------------------------------------------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| [foxws/laravel-docs](https://github.com/foxws/laravel-docs)                   | Pulls each package's `docs/` folder from GitHub into the database. Rendered by `src/Modules/Marketing` (controllers, `Http/Props`, `Support/DocsNavigation.php`). |
| [foxws/laravel-podman](https://github.com/foxws/laravel-podman)               | Local development containers, rendered from `containers/stubs`                                                                                                    |
| [foxws/laravel-ddd](https://github.com/foxws/laravel-ddd)                     | The `src/Domain`, `src/Modules`, `src/Foundation` and `src/Support` layout                                                                                        |
| [foxws/laravel-essentials](https://github.com/foxws/laravel-essentials)       | Application defaults, configured in `config/essentials.php`                                                                                                       |
| [foxws/laravel-pwa](https://github.com/foxws/laravel-pwa)                     | Web app manifest and service worker (`@pwaHead`/`@pwaSw` in `resources/views/app.blade.php`)                                                                      |
| [foxws/laravel-scout-builder](https://github.com/foxws/laravel-scout-builder) | Docs search in `SearchController`                                                                                                                                 |

## Layout

```text
src/
├── Domain/       Business logic: models, actions, states (users)
├── Modules/      Features served over HTTP; Marketing is the public site
├── Foundation/   Service providers and app-wide middleware
└── Support/      Framework glue: CSP presets, Inertia middleware, response cache profile
resources/js/
├── pages/        Inertia pages (HomePage, ProjectView, DocumentView, ...)
└── components/   Vue components, built on Nuxt UI
```

## Running it locally

You'll need PHP 8.3 or newer, Node 24 with pnpm, PostgreSQL and Valkey (or Redis). Development here runs in Podman containers through laravel-podman, but any setup with those services works.

```sh
composer run setup
composer run dev
```

The site is empty until it has documentation to show. Register a project and sync it:

```sh
php artisan docs:projects:add laravel-docs "Laravel Docs" --github=foxws/laravel-docs --sync
```

`--sync` registers a `latest` version tracking `main` as the default and syncs it right away. Use `docs:versions:add` to register further versions, such as a release tag, or to change the default with `--default`.

## Project pages

A project with `kind` set to anything other than `package` gets its own page at `/projects/{slug}` instead of a docs page. The page is filled from the `metadata` in the front matter of the project's `docs/index.md`:

```yaml
---
metadata:
    kind: personal
    desc: A streaming platform built with Laravel and Inertia.js.
    image: https://raw.githubusercontent.com/francoism90/stry/main/docs/screenshot.png
    technologies: [Laravel, Inertia.js, Vue]
    docs: https://example.com/stry
---
```

| Key            | Used for                                                                                                                                                                        |
| -------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| `desc`         | The one-line description under the title, and on the homepage.                                                                                                                  |
| `image`        | The 16:10 image next to the title, or an "Image coming soon" placeholder without one. It must be a full `https://` URL, such as the raw GitHub URL of a file in the repository. |
| `introduction` | Markdown shown below the hero. Without it, the page uses the Introduction or About section of the README that `docs:sync` stores, or the README's opening.                      |
| `technologies` | The "Built with" tags.                                                                                                                                                          |
| `docs`         | Where "Read the docs" links to. Without it, the button links to the project's docs on this site when it has any, and is hidden otherwise.                                       |

Changes reach the site with the next `docs:sync`, which runs nightly and clears the response cache. Front matter is only read from a synced version, so a project without any versions keeps the metadata it has until it gets one, for example from a GitHub release. Its README is still synced, since root files are read at `HEAD`.

## Tests

```sh
php artisan test --compact
composer run lint:check
```

## License

The source code is released under the MIT License. See [LICENSE](LICENSE).

The Foxws name, logo and icons, and the site's written content, aren't covered by that license: all rights reserved. If you reuse the code, replace them with your own.
