<script setup lang="ts">
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
  suffix: string
  prefix: string
  to: string
}

const results = ref<SearchResult[]>([])
const loading = ref(false)

let controller: AbortController | undefined

const search = useDebounceFn(async (term: string) => {
  controller?.abort()

  if (term.trim().length < 2) {
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

watch(query, (term) => search(term))

const groups = computed(() => [
  {
    id: 'results',
    ignoreFilter: true,
    items: results.value,
  },
])
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
        @close="open = false"
      >
        <template #item-label="{ item }">
          <div class="flex w-full items-center gap-3">
            <span class="w-13 shrink-0 font-mono text-[9px] tracking-wider text-neutral-500">{{ item.prefix }}</span>
            <div class="flex min-w-0 flex-col gap-0.5">
              <span class="font-sans text-[13px] font-semibold text-neutral-50">{{ item.label }}</span>
              <span class="font-sans text-[11px] text-neutral-500">{{ item.suffix }}</span>
            </div>
          </div>
        </template>
      </UCommandPalette>
    </template>
  </UModal>
</template>
