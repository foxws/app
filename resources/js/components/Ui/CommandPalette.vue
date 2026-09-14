<script setup lang="ts">
import { computed } from 'vue'

withDefaults(
  defineProps<{
    scope?: string
  }>(),
  {
    scope: undefined,
  },
)

const open = defineModel<boolean>('open', { default: false })

// Static placeholder results — wire up to real content search once the docs
// backend (foxws/laravel-docs) is populated.
const groups = computed(() => [
  {
    id: 'results',
    items: [
      { label: 'Podman runners', suffix: 'Configure rootless transcode workers', prefix: 'STRY' },
      { label: 'Shaka setup', suffix: 'Wire the player to a DASH manifest', prefix: 'STRY' },
      { label: 'Typed page props', suffix: 'Share validated props with the client', prefix: 'INERTIAUSE' },
      { label: 'Creating an algo', suffix: 'Generate and register an algorithm', prefix: 'ALGOS' },
    ],
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
        :groups="groups"
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
