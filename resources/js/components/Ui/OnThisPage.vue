<script setup lang="ts">
defineProps<{
  sections: string[]
}>()
</script>

<template>
  <div class="flex flex-col gap-1.75">
    <span class="mb-0.5 font-mono text-[10px] tracking-[.14em] text-neutral-500">ON THIS PAGE</span>
    <span
      v-for="s in sections"
      :key="s"
      class="border-l border-neutral-800 py-1 pl-2.75 font-sans text-xs leading-snug text-neutral-400"
    >{{ s }}</span>
  </div>
</template>
