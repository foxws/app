<script setup lang="ts">
import { useClipboard } from '@vueuse/core'
import { computed } from 'vue'

const props = defineProps<{
  command: string
}>()

const { copy, copied } = useClipboard({ source: computed(() => props.command), copiedDuring: 1600 })
</script>

<template>
  <div
    class="flex h-12 max-w-130 min-w-0 flex-[1_1_20rem] items-center gap-3 rounded-full bg-neutral-950/60 ps-4.5 pe-1.5 font-mono text-sm ring-1 ring-neutral-50/12 ring-inset"
  >
    <span
      aria-hidden="true"
      class="text-neutral-500"
      >$</span
    >
    <span class="min-w-0 flex-1 truncate text-neutral-200">{{ command }}</span>
    <button
      type="button"
      class="h-9 shrink-0 rounded-full px-3.5 text-xs tracking-wider text-gold-400 uppercase hover:bg-neutral-50/8"
      @click="copy()"
    >
      {{ copied ? 'Copied' : 'Copy' }}
    </button>
  </div>
</template>
