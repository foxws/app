<script setup lang="ts">
import type { DocsNavItem } from '@/types'
import type { ContentNavigationLink } from '@nuxt/ui/components/content/ContentNavigation.vue'
import UContentNavigation from '@nuxt/ui/components/content/ContentNavigation.vue'

defineProps<{
  nav: DocsNavItem[]
}>()
</script>

<template>
  <!--
    Every section stays open and acts as a label, so the whole tree is visible
    at once. A section's `trigger` classes sit next to its `link` classes
    without being merged, hence the `!` on the ones that differ. Nested
    levels reuse this `ui` and fold their `root` into the inner list, so
    spacing between sections goes on `list`, never on `root`.
  -->
  <UContentNavigation
    :navigation="nav as unknown as ContentNavigationLink[]"
    type="multiple"
    :collapsible="false"
    trailing-icon=""
    :ui="{
      list: 'mx-0 mt-0 flex flex-col gap-5.5',
      listWithChildren: 'ms-0 flex flex-col gap-0.5 border-s-0',
      itemWithChildren: 'data-[state=open]:mb-0',
      trigger:
        'pointer-events-none min-h-0! pt-0! pb-1.5! font-mono text-xs! font-normal! tracking-wider text-neutral-500! uppercase',
      link: 'min-h-9 min-w-0 px-3 py-1.5 text-sm leading-snug before:bg-transparent hover:before:bg-transparent aria-[current=page]:font-semibold',
      linkTitle: 'min-w-0 text-pretty whitespace-normal!',
      linkTrailingIcon: 'hidden',
    }"
  />
</template>
