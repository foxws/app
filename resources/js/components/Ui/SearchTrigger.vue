<script setup lang="ts">
import { useSearchPalette } from '@/composables/search'

withDefaults(
  defineProps<{
    label?: string
    block?: boolean
  }>(),
  {
    label: 'Search',
    block: false,
  },
)

const { open } = useSearchPalette()
</script>

<template>
  <UButton
    icon="i-lucide-search"
    color="neutral"
    variant="soft"
    :block="block"
    :aria-label="label"
    :class="[
      'rounded-full bg-neutral-900 py-0 ps-3.5 pe-1.5 font-sans text-sm font-normal text-neutral-400 ring-1 ring-neutral-800 ring-inset hover:bg-neutral-900 hover:ring-neutral-700',
      block ? 'h-11' : 'h-10',
    ]"
    :ui="{ leadingIcon: 'size-4' }"
    @click="open = true"
  >
    <span :class="['text-left', block ? 'flex-1' : 'min-w-22']">{{ label }}</span>
    <UKbd
      value="meta"
      :ui="{ base: 'h-auto rounded-full bg-neutral-950 px-2 py-0.5 font-mono text-xs text-neutral-400 ring-0' }"
    >
      ⌘K
    </UKbd>
  </UButton>
</template>
