<script setup lang="ts">
import DocsTree from '@/components/Ui/DocsTree.vue'

defineProps<{
  title: string
  nav: { group: string; items: string[] }[]
}>()

const open = defineModel<boolean>('open', { default: false })
</script>

<template>
  <USlideover
    v-model:open="open"
    side="bottom"
    :ui="{
      content: 'top-16 rounded-t-2xl border-t border-neutral-700 bg-neutral-900',
    }"
  >
    <template #content>
      <div class="flex items-center justify-between border-b border-neutral-800 px-4.5 py-3">
        <span class="font-sans text-[15px] font-semibold text-neutral-50">{{ title }}</span>
        <button
          type="button"
          class="rounded-full bg-neutral-800 px-2.5 py-1 font-mono text-[10px] text-neutral-300"
          @click="open = false"
        >
          CLOSE
        </button>
      </div>

      <div class="min-h-0 flex-1 overflow-y-auto p-3.5">
        <DocsTree :nav="nav" />
      </div>
    </template>
  </USlideover>
</template>
