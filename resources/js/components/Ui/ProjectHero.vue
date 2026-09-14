<script setup lang="ts">
defineProps<{
  eyebrow: string
  titleLines: [string, string]
  lead: string
}>()
</script>

<template>
  <div class="rounded-xl bg-identity-500 px-6 py-7.5 sm:px-6.5 sm:py-9">
    <div class="mb-3 font-mono text-[11px] tracking-[.18em] text-neutral-950/60">{{ eyebrow }}</div>
    <div class="font-sans text-[42px] leading-[.92] font-bold tracking-tight text-neutral-950 sm:text-[58px] sm:leading-[.9]">
      {{ titleLines[0] }}<template v-if="titleLines[1]"><br>{{ titleLines[1] }}</template>
    </div>
    <p class="mt-4.5 max-w-md font-sans text-[15px] leading-snug font-medium text-neutral-950/78 sm:mt-4.5">
      {{ lead }}
    </p>
  </div>
</template>
