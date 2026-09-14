<script setup lang="ts">
defineProps<{
  prev?: string
  next?: string
}>()
</script>

<template>
  <div class="flex gap-2.5 border-t border-neutral-900 pt-5">
    <div
      v-if="prev"
      class="flex flex-1 flex-col gap-0.75 rounded-lg border border-neutral-900 px-4 py-3.5 hover:border-neutral-700"
    >
      <span class="font-mono text-[11px] text-neutral-500">← PREVIOUS</span>
      <span class="font-sans text-sm font-semibold text-neutral-50">{{ prev }}</span>
    </div>
    <div
      v-if="next"
      class="flex flex-1 flex-col items-end gap-0.75 rounded-lg border border-neutral-900 px-4 py-3.5 text-right hover:border-neutral-700"
    >
      <span class="font-mono text-[11px] text-neutral-500">NEXT →</span>
      <span class="font-sans text-sm font-semibold text-neutral-50">{{ next }}</span>
    </div>
  </div>
</template>
