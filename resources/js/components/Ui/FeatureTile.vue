<script setup lang="ts">
defineProps<{
  t: string
  d: string
}>()
</script>

<template>
  <div class="flex items-start gap-2.5 rounded-lg border border-neutral-900 bg-neutral-950 p-3.5">
    <span class="mt-1.75 block size-1.25 shrink-0 rounded-full bg-foxrust-500" />
    <div class="flex flex-col gap-0.75">
      <span class="font-sans text-[13px] font-semibold text-neutral-50">{{ t }}</span>
      <span class="font-sans text-xs leading-snug text-neutral-400">{{ d }}</span>
    </div>
  </div>
</template>
