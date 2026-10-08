<?php

use Modules\Marketing\Support\ReadmeIntroduction;

it('uses the introduction section when the readme has one', function () {
    $readme = <<<'MD'
    # stry

    ## Video-on-Demand Platform

    ## Introduction

    **stry** is a video platform.

    ## Usage

    Run it.
    MD;

    expect(ReadmeIntroduction::extract($readme, 'francoism90/stry'))->toBe('**stry** is a video platform.');
});

it('uses what comes before the first section otherwise, without the title, badges, link rows or rules', function () {
    $readme = <<<'MD'
    # flatpaks [![build](https://example.com/badge.svg)](https://example.com/actions)

    [![Tests](https://example.com/tests.svg)](https://example.com/tests)
    ![License](https://img.shields.io/badge/license-MIT-blue)

    [Demo](https://example.com/demo) • [Documentation](#documentation)

    ---

    Unofficial Flatpak builds.

    ## Install
    MD;

    expect(ReadmeIntroduction::extract($readme, 'francoism90/flatpaks'))->toBe('Unofficial Flatpak builds.');
});

it('points relative links and images at the repository, leaving absolute ones alone', function () {
    $readme = 'See [recipes](recipes/), [the guide](./docs/guide.md#setup), [usage](#usage), [Flatter](https://github.com/andyholmes/flatter) and ![shot](/art/shot.png).';

    expect(ReadmeIntroduction::extract($readme, 'francoism90/personal-os'))->toBe(
        'See [recipes](https://github.com/francoism90/personal-os/blob/HEAD/recipes/), '
        .'[the guide](https://github.com/francoism90/personal-os/blob/HEAD/docs/guide.md#setup), '
        .'[usage](https://github.com/francoism90/personal-os#usage), '
        .'[Flatter](https://github.com/andyholmes/flatter) and '
        .'![shot](https://raw.githubusercontent.com/francoism90/personal-os/HEAD/art/shot.png).'
    );
});

it('turns github alerts into callouts', function () {
    $readme = <<<'MD'
    Intro.

    > [!WARNING]
    > Keep backups.

    > A plain quote.
    MD;

    expect(ReadmeIntroduction::extract($readme, 'francoism90/stry'))
        ->toBe("Intro.\n\n:::warning\nKeep backups.\n:::\n\n> A plain quote.");
});

it('leaves fenced code as it is, including headings and links inside it', function () {
    $readme = <<<'MD'
    Add the remote:

    ```bash
    ## not a section
    echo "[link](relative.md)"
    ```

    ## Install
    MD;

    expect(ReadmeIntroduction::extract($readme, 'francoism90/flatpaks'))
        ->toBe("Add the remote:\n\n```bash\n## not a section\necho \"[link](relative.md)\"\n```");
});

it('returns null when there is no introduction to show', function () {
    expect(ReadmeIntroduction::extract("# stry\n\n## Usage\n\nRun it.", 'francoism90/stry'))->toBeNull();
});
