<script setup lang="ts">
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
</script>

<template>
  <ULink
    :to="href ?? undefined"
    :target="isExternal ? '_blank' : undefined"
    raw
    class="group flex min-w-0 flex-col gap-2.5 border-t border-neutral-800 pt-5 focus-visible:outline-offset-6"
  >
    <span class="flex items-center gap-2">
      <h3 class="font-sans text-xl font-semibold tracking-tight text-neutral-50">{{ name }}</h3>
      <UIcon
        :name="isExternal ? 'i-lucide-arrow-up-right' : 'i-lucide-arrow-right'"
        class="size-4 shrink-0 text-neutral-600 transition-colors group-hover:text-neutral-300"
      />
    </span>

    <p class="max-w-88 font-sans text-sm text-pretty text-neutral-400">{{ desc }}</p>
  </ULink>
</template>
