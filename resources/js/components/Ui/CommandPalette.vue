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
  description: string
  descriptionHtml?: string
  to: string
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

const groups = computed(() => [
  {
    id: 'results',
    ignoreFilter: true,
    items: results.value,
  },
])

watch(query, (term) => search(term))
</script>

<template>
  <UModal
    v-model:open="open"
    :ui="{
      content: 'top-11 max-w-xl translate-y-0 bg-neutral-900 ring-1 ring-neutral-700',
    }"
  >
    <template #content>
      <UCommandPalette
        v-model:search-term="query"
        :groups="groups"
        :loading="loading"
        :placeholder="scope ? `Search ${scope}…` : 'Search…'"
        close
        :ui="{
          itemDescription: 'truncate font-sans text-xs text-neutral-600 [&_mark]:bg-primary/15 [&_mark]:text-primary',
          empty: 'font-sans text-sm text-neutral-500',
          itemTrailing: 'hidden',
        }"
        @close="open = false"
        @update:model-value="onSelect"
      >
        <template #item-label="{ item }">
          <div class="flex w-full items-center gap-3">
            <div class="flex min-w-0 flex-col gap-0.5">
              <span
                v-if="item.labelHtml"
                class="font-sans text-sm font-semibold text-neutral-50 [&_mark]:bg-primary/15 [&_mark]:text-primary"
                v-html="item.labelHtml"
              />
              <span
                v-else
                class="font-sans text-sm font-semibold text-neutral-50"
                >{{ item.label }}</span
              >

              <span
                v-if="item.suffixHtml"
                class="font-sans text-xs text-neutral-500 [&_mark]:bg-primary/15 [&_mark]:text-primary"
                v-html="item.suffixHtml"
              />
              <span
                v-else-if="item.suffix"
                class="font-sans text-xs text-neutral-500"
                >{{ item.suffix }}</span
              >
            </div>
          </div>
        </template>

        <template #empty="{ searchTerm }">
          <span v-if="searchTerm.trim().length > 0 && searchTerm.trim().length < minQueryLength">
            Type at least {{ minQueryLength }} characters to search
          </span>
          <span v-else-if="searchTerm">No results for "{{ searchTerm }}"</span>
          <span v-else>Search for a project, package, or docs detail</span>
        </template>
      </UCommandPalette>
    </template>
  </UModal>
</template>
