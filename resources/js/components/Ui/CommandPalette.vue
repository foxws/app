<script setup lang="ts">
import { router } from '@inertiajs/vue3'
import { useDebounceFn } from '@vueuse/core'
import { computed, ref, watch } from 'vue'

withDefaults(
  defineProps<{
    scope?: string
  }>(),
  {
    scope: undefined,
  },
)

const open = defineModel<boolean>('open', { default: false })
const query = defineModel<string>('searchTerm', { default: '' })

interface SearchResult {
  label: string
  labelHtml?: string
  suffix: string
  suffixHtml?: string
  prefix: string
  project: string
  section: string | null
  version: string
  description: string
  descriptionHtml?: string
  to: string
}

interface SearchGroup {
  id: string
  label: string
  version: string
  ignoreFilter: true
  items: SearchResult[]
}

// Kept in sync with the backend's own `query` => ['min:2', ...] rule
// (SearchController) — below this, there's nothing to search yet.
const minQueryLength = 2

const results = ref<SearchResult[]>([])
const loading = ref(false)

let controller: AbortController | undefined

const search = useDebounceFn(async (term: string) => {
  controller?.abort()

  if (term.trim().length < minQueryLength) {
    loading.value = false
    results.value = []

    return
  }

  controller = new AbortController()
  loading.value = true

  try {
    const response = await fetch(`/api/v1/search?${new URLSearchParams({ query: term })}`, {
      headers: { Accept: 'application/json' },
      signal: controller.signal,
    })

    results.value = response.ok ? await response.json() : []
  } catch (error) {
    if (!(error instanceof DOMException && error.name === 'AbortError')) {
      results.value = []
    }
  } finally {
    loading.value = false
  }
}, 200)

function onSelect(item: Partial<SearchResult>) {
  if (!item?.to) {
    return
  }

  open.value = false
  router.visit(item.to)
}

/** Results arrive ranked; grouping by project keeps each project's best hit first. */
const groups = computed<SearchGroup[]>(() => {
  const byProject = new Map<string, SearchGroup>()

  for (const result of results.value) {
    const group = byProject.get(result.prefix) ?? {
      id: result.prefix,
      label: result.project,
      version: result.version,
      ignoreFilter: true,
      items: [],
    }

    group.items.push(result)
    byProject.set(result.prefix, group)
  }

  return [...byProject.values()]
})

const countLabel = computed(() => {
  if (query.value.trim().length < minQueryLength) {
    return 'type to search'
  }

  return results.value.length === 1 ? '1 result' : `${results.value.length} results`
})

watch(query, (term) => search(term))
</script>

<template>
  <UModal
    v-model:open="open"
    :ui="{
      overlay: 'bg-neutral-950/72 backdrop-blur-sm',
      content:
        'top-[clamp(1rem,12vh,7rem)] max-h-[min(640px,80vh)] max-w-160 translate-y-0 overflow-hidden rounded-3xl shadow-[0_30px_80px_rgb(0_0_0/0.55)] ring-1 ring-neutral-700 bg-dialog-glow sm:max-w-160',
    }"
  >
    <template #content>
      <UCommandPalette
        v-model:search-term="query"
        :groups="groups"
        :loading="loading"
        :placeholder="scope ? `Search ${scope} and more` : 'Search projects, packages and docs'"
        :close="{
          label: 'esc',
          icon: '',
          color: 'neutral',
          variant: 'soft',
          class:
            'h-7 rounded-full bg-neutral-950 px-2.5 font-mono text-xs text-neutral-400 hover:bg-neutral-950 hover:text-neutral-50',
        }"
        :ui="{
          root: 'divide-neutral-800',
          input:
            'h-15 ps-2 pe-1 font-sans text-base text-neutral-50 focus-within:shadow-[inset_0_-2px_0_var(--ui-primary)] [&>input]:h-15 [&>input]:placeholder:text-neutral-500',
          viewport: 'divide-y-0 p-2',
          group: 'p-0 pb-1.5',
          label:
            'flex items-baseline justify-between gap-3 px-3 pt-2.5 pb-1.5 font-mono text-xs font-normal tracking-[.14em] text-neutral-500 uppercase',
          item: 'items-center gap-3 rounded-xl px-3 py-2.5 before:inset-0 before:rounded-xl hover:before:bg-neutral-800/45 data-highlighted:before:bg-neutral-800/80',
          itemLabel: 'whitespace-normal text-neutral-50',
          itemDescription: 'hidden',
          itemTrailingHighlightedIcon: 'size-4 text-neutral-400',
          empty: 'px-3 py-8 font-sans text-sm text-neutral-400',
          footer:
            'flex flex-wrap items-center justify-between gap-x-4 gap-y-1.5 border-neutral-800 px-5 py-2.5 font-mono text-xs tracking-wider text-neutral-400',
        }"
        @close="open = false"
        @update:model-value="onSelect"
      >
        <template #group-label="{ group }">
          <span>{{ group.label }}</span>
          <span class="tracking-wider normal-case">{{ (group as SearchGroup).version }}</span>
        </template>

        <template #item-label="{ item }">
          <span class="flex min-w-0 flex-col gap-0.5">
            <span class="flex flex-wrap items-baseline gap-x-2.5">
              <span
                v-if="item.labelHtml"
                class="font-sans text-[15px] leading-5.5 font-semibold text-neutral-50 [&_mark]:rounded-[3px] [&_mark]:bg-identity-500/28 [&_mark]:px-0.5 [&_mark]:text-neutral-50"
                v-html="item.labelHtml"
              />
              <span
                v-else
                class="font-sans text-[15px] leading-5.5 font-semibold text-neutral-50"
                >{{ item.label }}</span
              >
              <span
                v-if="item.section"
                class="font-mono text-xs tracking-wider text-neutral-500"
                >{{ item.section }}</span
              >
            </span>

            <span
              v-if="item.descriptionHtml"
              class="truncate font-sans text-sm text-neutral-400 [&_mark]:rounded-[3px] [&_mark]:bg-identity-500/28 [&_mark]:px-0.5 [&_mark]:text-neutral-50"
              v-html="item.descriptionHtml"
            />
            <span
              v-else-if="item.description"
              class="truncate font-sans text-sm text-neutral-400"
              >{{ item.description }}</span
            >
          </span>
        </template>

        <template #empty="{ searchTerm }">
          <span v-if="searchTerm.trim().length > 0 && searchTerm.trim().length < minQueryLength">
            Type at least {{ minQueryLength }} characters to search.
          </span>
          <span v-else-if="searchTerm">
            No results for <span class="font-mono text-neutral-50">{{ searchTerm }}</span
            >.
          </span>
          <span v-else>Search for a project, package, or docs detail.</span>
        </template>

        <template #footer>
          <span class="flex flex-wrap items-center gap-x-3.5 gap-y-1.5">
            <span><span class="text-neutral-50">↑↓</span> navigate</span>
            <span><span class="text-neutral-50">↵</span> open</span>
            <span><span class="text-neutral-50">esc</span> close</span>
          </span>
          <span>{{ countLabel }}</span>
        </template>
      </UCommandPalette>
    </template>
  </UModal>
</template>
