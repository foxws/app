<script setup lang="ts">
import { useMonogram } from '@/composables/monogram'
import { computed } from 'vue'

const props = withDefaults(
  defineProps<{
    name: string
    desc: string
    href?: string | null
  }>(),
  {
    href: null,
  },
)

const isExternal = computed(() => props.href?.startsWith('http') ?? false)

const { formatMonogram } = useMonogram()

const monogram = computed(() => formatMonogram(props.name))
</script>

<template>
  <ULink
    :to="href ?? undefined"
    :target="isExternal ? '_blank' : undefined"
    raw
    class="group flex min-w-0 flex-col gap-2.5 border-t border-neutral-800 pt-5 focus-visible:outline-offset-6"
  >
    <span
      aria-hidden="true"
      class="mb-1.5 flex size-14 items-center justify-center rounded-2xl bg-neutral-900 font-mono text-base tracking-wider text-gold-300 ring-1 ring-neutral-800 ring-inset"
    >
      {{ monogram }}
    </span>

    <span class="flex items-center gap-2">
      <h3 class="font-sans text-xl font-semibold tracking-tight text-neutral-50">{{ name }}</h3>
      <UIcon
        name="i-lucide-arrow-up-right"
        class="size-4 shrink-0 text-neutral-600 transition-colors group-hover:text-neutral-300"
      />
    </span>

    <p class="max-w-88 font-sans text-sm text-pretty text-neutral-400">{{ desc }}</p>
  </ULink>
</template>
