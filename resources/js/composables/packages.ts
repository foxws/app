import type { DocsPackageGroup } from '@/types'
import { computed, ref, toValue, type MaybeRefOrGetter } from 'vue'

const allGroups = 'all'
const ungroupedLabel = 'More'

export function usePackageFilter(packageGroups: MaybeRefOrGetter<DocsPackageGroup[]>) {
  const activeGroup = ref<string>(allGroups)
  const filter = ref('')

  const packages = computed(() =>
    toValue(packageGroups).flatMap((group) =>
      group.packages.map((pkg) => ({ ...pkg, group: group.name ?? ungroupedLabel })),
    ),
  )

  const chips = computed(() => [
    { label: 'All', value: allGroups, count: packages.value.length },
    ...toValue(packageGroups).map((group) => ({
      label: group.name ?? ungroupedLabel,
      value: group.name ?? ungroupedLabel,
      count: group.packages.length,
    })),
  ])

  /** Most-installed first, so the list leads with what people actually use. */
  const visiblePackages = computed(() => {
    const needle = filter.value.trim().toLowerCase()

    return packages.value
      .filter((pkg) => activeGroup.value === allGroups || pkg.group === activeGroup.value)
      .filter(
        (pkg) => needle === '' || [pkg.name, pkg.slug, pkg.desc, pkg.group].join(' ').toLowerCase().includes(needle),
      )
      .sort((a, b) => (b.downloads ?? 0) - (a.downloads ?? 0) || a.name.localeCompare(b.name))
  })

  function resetFilters() {
    activeGroup.value = allGroups
    filter.value = ''
  }

  return {
    activeGroup,
    filter,
    packages,
    chips,
    visiblePackages,
    resetFilters,
  }
}
