<script setup lang="ts">
import type { DocsPackageInfo } from '@/types'
import { computed } from 'vue'

const props = withDefaults(
  defineProps<{
    info: DocsPackageInfo
    variant?: 'list' | 'grid'
  }>(),
  { variant: 'list' },
)

const rows = computed(() =>
  (
    [
      ['Latest release', props.info.version],
      ['Requires', props.info.requires],
      ['Laravel', props.info.laravel],
      ['Runtime', props.info.runtime],
      ['Licence', props.info.licence],
    ] as [string, string | undefined][]
  ).filter((row): row is [string, string] => Boolean(row[1])),
)
</script>

<template>
  <div
    v-if="rows.length && variant === 'list'"
    class="flex flex-col gap-2"
  >
    <span class="font-mono text-[10px] tracking-[.14em] text-neutral-500 uppercase">Package</span>

    <div class="flex flex-col divide-y divide-neutral-900 border-t border-neutral-900">
      <div
        v-for="[label, value] in rows"
        :key="label"
        class="flex items-center justify-between py-2.5"
      >
        <span class="font-sans text-[13px] text-neutral-400">{{ label }}</span>
        <span class="font-mono text-[13px] text-neutral-50">{{ value }}</span>
      </div>
    </div>
  </div>

  <UPageGrid
    v-else-if="rows.length"
    :ui="{ base: 'grid-cols-2 gap-2.5 sm:grid-cols-2 lg:grid-cols-2' }"
  >
    <UPageCard
      v-for="[label, value] in rows"
      :key="label"
      :title="label"
      :description="value"
      variant="subtle"
      :ui="{
        container: 'gap-0 p-3.5 sm:p-3.5',
        title: 'font-sans text-[12px] font-normal text-neutral-500',
        description: 'mt-1 font-mono text-[15px] text-neutral-50',
      }"
    />
  </UPageGrid>
</template>
