<script setup lang="ts">
defineProps<{
  href: string
}>()
</script>

<template>
  <UPageCard
    title="Want to see all four working together?"
    variant="outline"
    :ui="{
      root: 'rounded-xl bg-neutral-900 ring-neutral-800',
      container: 'p-6 sm:p-6',
      wrapper: 'flex-col items-start gap-5 sm:flex-row sm:items-center sm:justify-between',
      body: 'max-w-xl',
      title: 'font-sans text-lg font-semibold text-neutral-50',
      description: 'mt-1.5 font-sans text-sm leading-relaxed text-neutral-400',
      footer: 'mt-0 shrink-0',
    }"
  >
    <template #description>
      Stry is the self-hosted video platform these packages were extracted from — transcoding, packaging, playback and
      catalogue, in one open-source app.
    </template>

    <template #footer>
      <UButton
        :to="href"
        class="rounded-lg bg-identity-500 px-5 py-3 font-sans text-[13px] font-semibold text-neutral-950 hover:bg-identity-400"
      >
        Explore Stry →
      </UButton>
    </template>
  </UPageCard>
</template>
