<script setup lang="ts">
import { useNumberFormat } from '@/composables/number'

defineProps<{
  name: string
  slug: string
  role?: string | null
  desc: string
  version?: string
  href: string
  downloads?: number | null
}>()

const { formatCompact, formatFull } = useNumberFormat()
</script>

<template>
  <UPageCard
    :to="href"
    variant="outline"
    :ui="{
      root: 'min-h-39.5 rounded-xl bg-neutral-900 ring-neutral-800 hover:bg-neutral-800/60 hover:ring-neutral-700',
      container: 'p-5.5 sm:p-5.5',
      wrapper: 'items-stretch gap-2.75',
      header: 'mb-0',
      description: 'font-sans text-sm text-neutral-400',
      footer: 'mt-0 border-t border-neutral-800 pt-2.75',
    }"
  >
    <template #header>
      <div class="flex items-center justify-between gap-2.5">
        <h3 class="font-sans text-xl font-semibold tracking-tight text-neutral-50">{{ name }}</h3>
        <span
          v-if="role"
          class="font-mono text-xs tracking-wider text-identity-500 uppercase"
          >{{ role }}</span
        >
      </div>
    </template>

    <template #description>{{ desc }}</template>

    <template #footer>
      <div class="flex items-center justify-between">
        <span class="font-mono text-xs text-neutral-500">{{ slug }}</span>
        <div class="flex items-center gap-3">
          <span
            v-if="downloads != null"
            class="inline-flex items-center gap-1 font-mono text-xs text-neutral-500"
            :title="`${formatFull(downloads)} installs in the last 30 days`"
          >
            <UIcon
              name="i-lucide-download"
              class="size-3"
            />
            {{ formatCompact(downloads) }}/mo
          </span>
          <span
            v-if="version"
            class="font-mono text-xs text-neutral-400"
            >{{ version }}</span
          >
        </div>
      </div>
    </template>
  </UPageCard>
</template>
