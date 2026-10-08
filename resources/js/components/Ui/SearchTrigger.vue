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
  <!-- Icon-only on phones, where the label and shortcut would crowd out the breadcrumb. -->
  <UButton
    icon="i-lucide-search"
    color="neutral"
    variant="soft"
    :block="block"
    :aria-label="label"
    :class="[
      'rounded-full bg-neutral-900 py-0 font-sans text-sm font-normal text-neutral-400 ring-1 ring-neutral-800 ring-inset hover:bg-neutral-900 hover:ring-neutral-700',
      block ? 'h-11 ps-3.5 pe-1.5' : 'size-10 justify-center px-0 sm:w-auto sm:justify-start sm:ps-3.5 sm:pe-1.5',
    ]"
    :ui="{ leadingIcon: 'size-4' }"
    @click="open = true"
  >
    <span :class="['text-left', block ? 'flex-1' : 'hidden min-w-22 sm:inline']">{{ label }}</span>
    <UKbd
      value="meta"
      :class="block ? undefined : 'hidden sm:inline-flex'"
      :ui="{ base: 'h-auto rounded-full bg-neutral-950 px-2 py-0.5 font-mono text-xs text-neutral-400 ring-0' }"
    >
      ⌘K
    </UKbd>
  </UButton>
</template>
