<script setup lang="ts">
// Not auto-registered outside a @nuxt/content app (see vite.config.ts's `content: true`) —
// imported directly and fed our own Inertia-provided nav instead of a Content collection.
import UContentNavigation from '@nuxt/ui/components/content/ContentNavigation.vue'
import type { ContentNavigationLink } from '@nuxt/ui/components/content/ContentNavigation.vue'

defineProps<{
  nav: { title: string; path?: string; children?: { title: string; path: string }[] }[]
}>()
</script>

<template>
  <UContentNavigation
    :navigation="(nav as unknown as ContentNavigationLink[])"
    highlight
  />
</template>
