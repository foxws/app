<script setup lang="ts">
import { useAppearance } from '@/composables/appearance'

withDefaults(
  defineProps<{
    location?: string
    label?: boolean
    size?: number
  }>(),
  {
    label: true,
    size: 28,
  },
)

const { app } = useAppearance()
</script>

<template>
  <ULink
    class="flex items-center gap-2.5"
    :to="location || '/'"
  >
    <img
      src="https://avatars.githubusercontent.com/u/111247253?s=400&v=4"
      alt="Foxws"
      class="block shrink-0 rounded-none object-cover"
      :style="{ width: `${size}px`, height: `${size}px` }"
    />
    <span
      v-if="label"
      class="font-mono text-[13px] text-neutral-50"
    >{{ app }}</span>
  </ULink>
</template>
