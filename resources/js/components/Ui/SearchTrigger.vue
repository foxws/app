<script setup lang="ts">
import { defineShortcuts } from '@nuxt/ui/composables'

withDefaults(
  defineProps<{
    scope?: string
  }>(),
  {
    scope: undefined,
  },
)

const open = defineModel<boolean>('open', { default: false })

defineShortcuts({ meta_k: () => (open.value = true) })
</script>

<template>
  <UButton
    icon="i-lucide-search"
    variant="ghost"
    color="neutral"
    :aria-label="scope ? `Search ${scope}` : 'Search'"
    @click="open = true"
  />
</template>
