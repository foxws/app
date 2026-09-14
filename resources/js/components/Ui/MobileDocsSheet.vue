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
    :ui="{
      content: 'max-h-[70vh] rounded-t-2xl border-t border-neutral-700 bg-neutral-900',
    }"
  >
    <template #content>
      <div class="flex items-center justify-between border-b border-neutral-800 px-4.5 py-3">
        <span class="font-sans text-[15px] font-semibold text-neutral-50">{{ title }}</span>
        <button
          type="button"
          class="rounded-full bg-neutral-800 px-2.5 py-1 font-mono text-[10px] text-neutral-300 uppercase"
          @click="open = false"
        >
          Close
        </button>
      </div>

      <div
        class="min-h-0 flex-1 overflow-y-auto p-3.5"
        @click.capture="(e) => (e.target as HTMLElement).closest('a') && (open = false)"
      >
        <DocsTree :nav="nav" />
      </div>
    </template>
  </USlideover>
</template>
