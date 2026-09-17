<script setup lang="ts">
defineProps<{
  eyebrow: string
  title: string
  lead: string
}>()
</script>

<template>
  <div class="rounded-xl bg-identity-500 px-6 py-7.5 sm:px-6.5 sm:py-9">
    <div
      v-if="eyebrow"
      class="mb-3 font-mono text-xs tracking-[.18em] text-neutral-950/60"
    >
      {{ eyebrow }}
    </div>

    <div class="font-sans text-5xl leading-[.92] font-bold tracking-tight text-neutral-950 sm:text-6xl sm:leading-[.9]">
      {{ title }}
    </div>

    <p
      v-if="lead"
      class="mt-4.5 max-w-md font-sans text-base leading-snug font-medium text-neutral-950/78 sm:mt-4.5"
    >
      {{ lead }}
    </p>
  </div>
</template>
