<script setup lang="ts">
import { useNumberFormat } from '@/composables/number'
import type { DocsPackageInfo } from '@/types'
import { computed } from 'vue'

const props = defineProps<{
  info: DocsPackageInfo
  downloads?: number | null
}>()

const { formatCompact } = useNumberFormat()

const rows = computed(() =>
  (
    [
      ['Latest release', props.info.version],
      ['Requires', props.info.requires],
      ['Laravel', props.info.laravel],
      ['Runtime', props.info.runtime],
      ['Licence', props.info.licence],
      ['Downloads', props.downloads ? `${formatCompact(props.downloads)}/mo` : undefined],
    ] as [string, string | undefined][]
  ).filter((row): row is [string, string] => Boolean(row[1])),
)
</script>

<template>
  <dl
    v-if="rows.length"
    class="grid grid-cols-[repeat(auto-fit,minmax(min(100%,8.125rem),1fr))] gap-x-6 gap-y-5"
  >
    <div
      v-for="[label, value] in rows"
      :key="label"
      class="flex flex-col gap-1"
    >
      <dt class="font-mono text-xs tracking-[.14em] text-neutral-400 uppercase">{{ label }}</dt>
      <dd class="font-mono text-sm text-neutral-50">{{ value }}</dd>
    </div>
  </dl>
</template>
