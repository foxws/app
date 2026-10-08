<script setup lang="ts">
import { useNumberFormat } from '@/composables/number'

defineProps<{
  name: string
  desc: string
  href: string
  downloads?: number | null
}>()

const { formatCompact, formatFull } = useNumberFormat()
</script>

<template>
  <ULink
    :to="href"
    raw
    class="group flex min-h-14 break-inside-avoid items-center gap-x-6 gap-y-3 border-t border-neutral-900 py-3.5"
  >
    <span class="flex min-w-0 flex-1 flex-col gap-0.5">
      <span class="font-sans text-base font-semibold text-neutral-50">{{ name }}</span>
      <span class="truncate font-sans text-sm text-neutral-400">{{ desc }}</span>
    </span>

    <span
      v-if="downloads != null"
      class="inline-flex min-w-19 shrink-0 items-center justify-end gap-1 font-mono text-xs tracking-wider text-neutral-400"
      :title="`${formatFull(downloads)} installs in the last 30 days`"
    >
      <UIcon
        name="i-lucide-download"
        class="size-3"
      />
      {{ formatCompact(downloads) }}/mo
    </span>

    <UIcon
      name="i-lucide-arrow-right"
      class="size-4 shrink-0 text-neutral-600 transition-colors group-hover:text-neutral-300"
    />
  </ULink>
</template>
