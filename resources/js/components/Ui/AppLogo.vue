<script setup lang="ts">
import { useAppearance } from '@/composables/appearance'
import logo from '@images/logo.png'

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
      :src="logo"
      alt="Foxws"
      class="block shrink-0 rounded-none object-cover"
      :style="{ width: `${size}px`, height: `${size}px` }"
    />

    <span
      v-if="label"
      class="font-mono text-sm text-neutral-50 lowercase"
    >
      {{ app }}
    </span>
  </ULink>
</template>
