<script setup lang="ts">
defineProps<{
  href: string
}>()
</script>

<template>
  <div class="flex flex-col items-start gap-5 rounded-xl border border-neutral-800 bg-neutral-900 p-6 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex max-w-xl flex-col gap-1.5">
      <span class="font-sans text-lg font-semibold text-neutral-50">Want to see all four working together?</span>
      <span class="font-sans text-sm leading-relaxed text-neutral-400">
        Stry is the self-hosted video platform these packages were extracted from — transcoding, packaging,
        playback and catalogue, in one open-source app.
      </span>
    </div>

    <UButton
      :to="href"
      class="shrink-0 rounded-lg bg-identity-500 px-5 py-3 font-sans text-[13px] font-semibold text-neutral-950 hover:bg-identity-400"
    >
      Explore Stry →
    </UButton>
  </div>
</template>
