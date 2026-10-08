import { usePackageFilter } from '@/composables/packages'
import type { DocsPackageGroup, DocsPackageSummary } from '@/types'
import { expect, test } from 'vite-plus/test'

function pkg(name: string, downloads: number | null, desc = ''): DocsPackageSummary {
  return { name, slug: name.toLowerCase(), href: `/${name.toLowerCase()}`, role: null, desc, downloads }
}

const groups: DocsPackageGroup[] = [
  { name: 'Media', packages: [pkg('Shaka', 977), pkg('Streamer', 2200)] },
  { name: null, packages: [pkg('Docs', null, 'Import a docs folder')] },
]

test('lists every package, most installed first', () => {
  const { visiblePackages } = usePackageFilter(groups)

  expect(visiblePackages.value.map((p) => p.name)).toEqual(['Streamer', 'Shaka', 'Docs'])
})

test('counts each group, labelling packages without one as "More"', () => {
  const { chips } = usePackageFilter(groups)

  expect(chips.value.map((chip) => [chip.label, chip.count])).toEqual([
    ['All', 3],
    ['Media', 2],
    ['More', 1],
  ])
})

test('narrows by group and by text in the name or description, and resets both', () => {
  const { activeGroup, filter, visiblePackages, resetFilters } = usePackageFilter(groups)

  activeGroup.value = 'Media'
  expect(visiblePackages.value.map((p) => p.name)).toEqual(['Streamer', 'Shaka'])

  activeGroup.value = 'all'
  filter.value = ' DOCS folder '
  expect(visiblePackages.value.map((p) => p.name)).toEqual(['Docs'])

  resetFilters()
  expect(visiblePackages.value).toHaveLength(3)
})
