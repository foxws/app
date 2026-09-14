<script setup lang="ts">
defineProps<{
  meta: { k: string; v: string }[]
}>()
</script>

<template>
  <div class="flex flex-col gap-2.25">
    <span class="font-mono text-[10px] tracking-[.14em] text-neutral-500">PACKAGE</span>
    <div
      v-for="m in meta"
      :key="m.k"
      class="flex items-baseline justify-between border-b border-neutral-900 pb-1.75"
    >
      <span class="font-sans text-xs text-neutral-400">{{ m.k }}</span>
      <span class="font-mono text-xs text-neutral-200">{{ m.v }}</span>
    </div>
  </div>
</template>
