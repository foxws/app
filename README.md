# Foxws website

## Documentation projects

Docs are pulled from GitHub into the database by [foxws/laravel-docs](https://github.com/foxws/laravel-docs). Register a project and sync it:

```sh
php artisan docs:projects:add laravel-docs "Laravel Docs" --github=foxws/laravel-docs --sync
php artisan docs:versions:add laravel-docs latest main --default
```

`--sync` already registers a "latest" version tracking "main" as the default and syncs it immediately; use `docs:versions:add` on its own to register additional versions (e.g. a stable release tag) or to change which one is the default via `--default`.

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).
