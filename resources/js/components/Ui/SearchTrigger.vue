<script setup lang="ts">
import { defineShortcuts } from '@nuxt/ui/composables'

withDefaults(
  defineProps<{
    scope?: string
  }>(),
  {
    scope: undefined,
  },
)

const open = defineModel<boolean>('open', { default: false })

defineShortcuts({ meta_k: () => (open.value = true) })
</script>

<template>
  <button
    type="button"
    class="hidden items-center gap-2 rounded-full border border-neutral-800 bg-neutral-900 px-3 py-1.5 text-neutral-400 transition-colors hover:border-neutral-700 sm:flex"
    @click="open = true"
  >
    <span class="block size-2.75 shrink-0 rounded-full border-[1.5px] border-current" />
    <span class="font-sans text-xs">{{ scope ? `Search ${scope}` : 'Search' }}</span>
    <span class="rounded bg-neutral-800 px-1.25 py-px font-mono text-[10px] text-neutral-300">⌘K</span>
  </button>

  <button
    type="button"
    class="flex size-9.5 shrink-0 items-center justify-center rounded-full border border-neutral-800 bg-neutral-900 text-neutral-400 sm:hidden"
    aria-label="Search"
    @click="open = true"
  >
    <span class="block size-3.25 rounded-full border-[1.5px] border-current" />
  </button>
</template>
