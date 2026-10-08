<?php

declare(strict_types=1);

namespace Modules\Marketing\Support;

use Illuminate\Support\Str;

/**
 * The introduction part of a README, for a project page: its
 * "Introduction" (or "About", "Overview") section when it has one, else
 * whatever comes before the first `##` heading. The title, badges, link
 * rows and rules around it are dropped, relative links and images point
 * at the repository on GitHub, since they'd break on this site, and
 * GitHub's alerts become the docs' own callouts.
 */
final class ReadmeIntroduction
{
    private const array SECTION_NAMES = ['introduction', 'about', 'overview'];

    public static function extract(string $readme, string $repository): ?string
    {
        $sections = self::sections($readme);

        $section = collect($sections)
            ->first(fn (array $section): bool => in_array(self::normalizeHeading($section['heading']), self::SECTION_NAMES, true))
            ?? $sections[0];

        $markdown = collect(self::convertAlerts($section['lines']))
            ->map(fn (array $line): ?string => $line['code'] ? $line['text'] : self::clean($line['text'], $repository))
            ->filter(fn (?string $line): bool => $line !== null)
            ->implode("\n");

        $markdown = trim((string) preg_replace("/\n{3,}/", "\n\n", $markdown));

        return $markdown !== '' ? $markdown : null;
    }

    /**
     * Split on `##` headings outside fenced code, keeping the part before
     * the first one as a section without a heading.
     *
     * @return array<int, array{heading: ?string, lines: array<int, array{text: string, code: bool}>}>
     */
    private static function sections(string $readme): array
    {
        $sections = [['heading' => null, 'lines' => []]];
        $inFence = false;

        foreach (preg_split('/\R/', $readme) ?: [] as $text) {
            if (preg_match('/^\s*(```|~~~)/', $text)) {
                $inFence = ! $inFence;
                $sections[array_key_last($sections)]['lines'][] = ['text' => $text, 'code' => true];

                continue;
            }

            if (! $inFence && preg_match('/^##\s+(.+)$/', $text, $heading)) {
                $sections[] = ['heading' => $heading[1], 'lines' => []];

                continue;
            }

            $sections[array_key_last($sections)]['lines'][] = ['text' => $text, 'code' => $inFence];
        }

        return $sections;
    }

    /**
     * GitHub's `> [!WARNING]` alerts become the `:::warning` callouts the
     * docs renderer knows; any other quote is left as it is.
     *
     * @param  array<int, array{text: string, code: bool}>  $lines
     * @return array<int, array{text: string, code: bool}>
     */
    private static function convertAlerts(array $lines): array
    {
        $converted = [];
        $inAlert = false;

        foreach ($lines as $line) {
            if (! $line['code'] && preg_match('/^\s*>\s*\[!(\w+)\]\s*$/', $line['text'], $alert)) {
                if ($inAlert) {
                    $converted[] = ['text' => ':::', 'code' => false];
                }

                $converted[] = ['text' => ':::'.Str::lower($alert[1]), 'code' => false];
                $inAlert = true;

                continue;
            }

            if ($inAlert && ! $line['code'] && preg_match('/^\s*>\s?(.*)$/', $line['text'], $quoted)) {
                $converted[] = ['text' => $quoted[1], 'code' => false];

                continue;
            }

            if ($inAlert) {
                $converted[] = ['text' => ':::', 'code' => false];
                $inAlert = false;
            }

            $converted[] = $line;
        }

        if ($inAlert) {
            $converted[] = ['text' => ':::', 'code' => false];
        }

        return $converted;
    }

    private static function normalizeHeading(?string $heading): string
    {
        return Str::of((string) $heading)
            ->replaceMatches('/[^\pL\s]/u', '')
            ->squish()
            ->lower()
            ->toString();
    }

    /**
     * Null drops the line: the title, a row of badges or links, or a rule.
     */
    private static function clean(string $line, string $repository): ?string
    {
        $link = '\[[^\]]*\]\([^)]*\)';
        $image = '!\[[^\]]*\]\([^)]*\)';

        $isTitle = preg_match('/^#\s/', $line);
        $isBadgeRow = preg_match("/^\s*((\[{$image}\]\([^)]*\)|{$image})\s*)+$/", $line);
        $isLinkRow = preg_match("/^\s*{$link}(\s*[•·|]\s*{$link})+\s*$/u", $line);
        $isRule = preg_match('/^\s*([-*_])(\s*\1){2,}\s*$/', $line);

        if ($isTitle || $isBadgeRow || $isLinkRow || $isRule) {
            return null;
        }

        return (string) preg_replace_callback(
            '/(!?)\[([^\]]*)\]\(([^)\s]+)\)/',
            fn (array $match): string => "{$match[1]}[{$match[2]}](".self::absoluteUrl($match[3], $repository, $match[1] === '!').')',
            $line,
        );
    }

    private static function absoluteUrl(string $url, string $repository, bool $isImage): string
    {
        if (preg_match('/^([a-z][a-z0-9+.-]*:|\/\/)/i', $url)) {
            return $url;
        }

        if (str_starts_with($url, '#')) {
            return "https://github.com/{$repository}{$url}";
        }

        $path = ltrim((string) preg_replace('/^\.\//', '', $url), '/');

        return $isImage
            ? "https://raw.githubusercontent.com/{$repository}/HEAD/{$path}"
            : "https://github.com/{$repository}/blob/HEAD/{$path}";
    }
}
