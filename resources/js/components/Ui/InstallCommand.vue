<script setup lang="ts">
import { useClipboard } from '@vueuse/core'
import { computed } from 'vue'

const props = defineProps<{
  command: string
}>()

const { copy, copied } = useClipboard({ source: computed(() => props.command), copiedDuring: 1200 })
</script>

<template>
  <div class="flex max-w-lg items-stretch overflow-hidden rounded-lg border border-neutral-800 bg-neutral-900">
    <div class="flex-1 overflow-x-auto px-3.5 py-3 font-mono text-sm whitespace-nowrap text-neutral-200">
      <span class="text-neutral-600">$ </span>{{ command }}
    </div>
    <button
      type="button"
      class="shrink-0 border-l border-neutral-800 px-4 font-mono text-[11px] tracking-wider text-identity-400 uppercase"
      @click="copy()"
    >
      {{ copied ? 'Copied' : 'Copy' }}
    </button>
  </div>
</template>
