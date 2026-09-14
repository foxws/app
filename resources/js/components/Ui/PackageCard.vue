<script setup lang="ts">
defineProps<{
  name: string
  slug: string
  role?: string | null
  desc: string
  version?: string
  href: string
}>()
</script>

<template>
  <ULink
    :to="href"
    class="flex min-h-[158px] flex-col gap-2.75 rounded-xl border border-neutral-800 bg-neutral-900 p-5.5 transition-colors hover:border-neutral-700 hover:bg-neutral-800/60"
  >
    <div class="flex items-center justify-between gap-2.5">
      <span class="font-sans text-xl font-semibold tracking-tight text-neutral-50">{{ name }}</span>
      <span
        v-if="role"
        class="font-mono text-[9px] tracking-wider text-foxrust-500"
      >{{ role }}</span>
    </div>

    <p class="flex-1 font-sans text-sm text-neutral-400">{{ desc }}</p>

    <div class="flex items-center justify-between border-t border-neutral-800 pt-2.75">
      <span class="font-mono text-[11px] text-neutral-500">{{ slug }}</span>
      <span
        v-if="version"
        class="font-mono text-[11px] text-neutral-400"
      >{{ version }}</span>
    </div>
  </ULink>
</template>
