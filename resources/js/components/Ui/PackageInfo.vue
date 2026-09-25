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
    <h2 class="font-mono text-xs tracking-[.14em] text-neutral-500 uppercase">Package</h2>

    <UPageList
      divide
      class="border-t border-neutral-900"
    >
      <UPageCard
        v-for="[label, value] in rows"
        :key="label"
        variant="ghost"
        :ui="{
          container: 'gap-0 p-0 py-2.5 sm:p-0 sm:py-2.5',
          wrapper: 'items-stretch',
          body: 'flex items-center justify-between gap-3',
        }"
      >
        <template #body>
          <span class="font-sans text-sm text-neutral-400">{{ label }}</span>
          <span class="font-mono text-sm text-neutral-50">{{ value }}</span>
        </template>
      </UPageCard>
    </UPageList>
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
        title: 'font-sans text-xs font-normal text-neutral-500',
        description: 'mt-1 font-mono text-base text-neutral-50',
      }"
    />
  </UPageGrid>
</template>
