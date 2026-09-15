<script setup lang="ts">
defineProps<{
  name: string
  desc?: string
  href: string
}>()
</script>

<template>
  <UPageCard
    :to="href"
    :title="`Used by ${name}`"
    variant="outline"
    :ui="{
      root: 'rounded-xl bg-neutral-900 ring-neutral-800 hover:bg-neutral-800/60 hover:ring-neutral-700',
      container: 'p-4.5 sm:p-4.5',
      title: 'font-sans text-sm font-semibold text-neutral-50',
      description: 'font-sans text-[13px] text-neutral-400',
    }"
  >
    <template
      v-if="desc"
      #description
      >{{ desc }} →</template
    >
  </UPageCard>
</template>
