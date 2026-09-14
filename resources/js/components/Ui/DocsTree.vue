<script setup lang="ts">
import type { DocsNavItem } from '@/types'
import type { ContentNavigationLink } from '@nuxt/ui/components/content/ContentNavigation.vue'
// Not auto-registered outside a @nuxt/content app (see vite.config.ts's `content: true`) —
// imported directly and fed our own Inertia-provided nav instead of a Content collection.
import UContentNavigation from '@nuxt/ui/components/content/ContentNavigation.vue'

defineProps<{
  nav: DocsNavItem[]
}>()
</script>

<template>
  <UContentNavigation
    :navigation="nav as unknown as ContentNavigationLink[]"
    highlight
    :ui="{ link: 'min-w-0', linkTitle: 'min-w-0 truncate' }"
  />
</template>
