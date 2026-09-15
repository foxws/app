<script setup lang="ts">
defineProps<{
  name: string
  desc?: string
  href: string
}>()
</script>

<template>
  <ULink
    :to="href"
    class="flex flex-col gap-1 rounded-xl border border-neutral-800 bg-neutral-900 p-4.5 transition-colors hover:border-neutral-700 hover:bg-neutral-800/60"
  >
    <span class="font-sans text-sm font-semibold text-neutral-50">Used by {{ name }}</span>
    <span
      v-if="desc"
      class="font-sans text-[13px] text-neutral-400"
      >{{ desc }} →</span
    >
  </ULink>
</template>
