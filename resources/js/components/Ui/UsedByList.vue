<script setup lang="ts">
import type { DocsUsedBy } from '@/types'

defineProps<{
  projects: DocsUsedBy[]
}>()

const destination = (href: string): string => {
  try {
    const url = new URL(href)

    return `${url.host}${url.pathname}`.replace(/\/$/, '')
  } catch {
    return href
  }
}
</script>

<template>
  <div class="flex flex-col gap-2">
    <h2 class="font-mono text-xs tracking-[.14em] text-neutral-500 uppercase">Used by</h2>

    <UPageList
      divide
      class="border-t border-neutral-900"
    >
      <UPageCard
        v-for="project in projects"
        :key="project.href"
        :to="project.href"
        target="_blank"
        variant="ghost"
        :ui="{
          root: 'group',
          container: 'gap-0 p-0 py-3 sm:p-0 sm:py-3',
          wrapper: 'items-stretch gap-1',
          body: 'flex flex-col gap-1',
        }"
      >
        <template #body>
          <span class="flex items-center justify-between gap-2">
            <span class="font-sans text-sm font-semibold text-neutral-50">{{ project.name }}</span>
            <UIcon
              name="i-lucide-arrow-up-right"
              class="size-4 shrink-0 text-neutral-500 transition-colors group-hover:text-neutral-300"
            />
          </span>
          <span
            v-if="project.desc"
            class="font-sans text-sm text-neutral-400"
            >{{ project.desc }}</span
          >
          <span class="truncate font-mono text-xs text-neutral-500">{{ destination(project.href) }}</span>
        </template>
      </UPageCard>
    </UPageList>
  </div>
</template>
