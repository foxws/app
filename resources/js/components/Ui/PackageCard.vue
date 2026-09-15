<script setup lang="ts">
defineProps<{
  name: string
  slug: string
  role?: string | null
  desc: string
  version?: string
  href: string
}>()
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
        <span class="font-sans text-xl font-semibold tracking-tight text-neutral-50">{{ name }}</span>
        <span
          v-if="role"
          class="font-mono text-[9px] tracking-wider text-identity-500 uppercase"
          >{{ role }}</span
        >
      </div>
    </template>

    <template #description>{{ desc }}</template>

    <template #footer>
      <div class="flex items-center justify-between">
        <span class="font-mono text-[11px] text-neutral-500">{{ slug }}</span>
        <span
          v-if="version"
          class="font-mono text-[11px] text-neutral-400"
          >{{ version }}</span
        >
      </div>
    </template>
  </UPageCard>
</template>
