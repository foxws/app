<script setup lang="ts">
import DocsTree from '@/components/Ui/DocsTree.vue'
import type { DocsNavItem } from '@/types'

defineProps<{
  title: string
  nav: DocsNavItem[]
}>()

const open = defineModel<boolean>('open', { default: false })
</script>

<template>
  <USlideover
    v-model:open="open"
    side="bottom"
    :title="title"
    :close="{
      label: 'Close',
      icon: '',
      color: 'neutral',
      variant: 'soft',
      class:
        'rounded-full bg-neutral-800 px-2.5 py-1 font-mono text-[10px] text-neutral-300 uppercase hover:bg-neutral-700',
    }"
    :ui="{
      content: 'max-h-[70vh] rounded-t-2xl border-t border-neutral-700 bg-neutral-900',
      header: 'min-h-0 border-b border-neutral-800 px-4.5 py-3 sm:px-4.5 sm:py-3',
      title: 'font-sans text-[15px] font-semibold text-neutral-50',
      body: 'min-h-0 flex-1 overflow-y-auto p-3.5 sm:p-3.5',
    }"
  >
    <template #body>
      <div @click.capture="(e) => (e.target as HTMLElement).closest('a') && (open = false)">
        <DocsTree :nav="nav" />
      </div>
    </template>
  </USlideover>
</template>
