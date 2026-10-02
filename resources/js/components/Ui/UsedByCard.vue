<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  name: string
  desc?: string
  href: string
}>()

/** Where the link goes, e.g. "github.com/francoism90/stry", so it's clear before clicking. */
const destination = computed(() => {
  try {
    const url = new URL(props.href)

    return `${url.host}${url.pathname}`.replace(/\/$/, '')
  } catch {
    return props.href
  }
})
</script>

<template>
  <div class="flex flex-col gap-2">
    <h2 class="font-mono text-xs tracking-[.14em] text-neutral-500 uppercase">Used by</h2>

    <!--
      A real border, not Nuxt UI's outline ring: the ring is drawn outside the
      card, and the right rail's overflow-x-hidden clips it at the corners.
    -->
    <UPageCard
      :to="href"
      target="_blank"
      variant="ghost"
      :ui="{
        root: 'group rounded-xl border border-neutral-800 bg-neutral-900 transition-colors hover:border-neutral-700 hover:bg-neutral-800/60',
        container: 'gap-1.5 p-4 sm:p-4',
        wrapper: 'items-stretch gap-1.5',
        header: 'mb-0',
        description: 'font-sans text-sm text-neutral-400',
        footer: 'mt-0 pt-1',
      }"
    >
      <template #header>
        <div class="flex items-center justify-between gap-2">
          <span class="font-sans text-sm font-semibold text-neutral-50">{{ name }}</span>
          <UIcon
            name="i-lucide-arrow-up-right"
            class="size-4 shrink-0 text-neutral-500 transition-colors group-hover:text-neutral-300"
          />
        </div>
      </template>

      <template
        v-if="desc"
        #description
        >{{ desc }}</template
      >

      <template #footer>
        <span class="block truncate font-mono text-xs text-neutral-500">{{ destination }}</span>
      </template>
    </UPageCard>
  </div>
</template>
