<script setup lang="ts">
import DocsToc from '@/components/Ui/DocsToc.vue'
import DocsTree from '@/components/Ui/DocsTree.vue'
import MobileDocsSheet from '@/components/Ui/MobileDocsSheet.vue'
import ProjectHero from '@/components/Ui/ProjectHero.vue'
import VersionSwitcher from '@/components/Ui/VersionSwitcher.vue'
import type { DocsDocument, DocsProject } from '@/types'
import { computed, ref } from 'vue'

/**
 * Inertia spreads the full page props onto every layout in the chain, so
 * `document`/`project` below arrive automatically — pages don't pass them in.
 * Only one of the two is ever set, depending on which page rendered.
 */
const props = defineProps<{
  document?: DocsDocument
  project?: DocsProject
}>()

const nav = computed(() => props.document?.nav ?? props.project?.nav ?? [])
const toc = computed(() => props.document?.toc ?? props.project?.overview?.toc ?? [])
const docsTitle = computed(() => props.document?.project.name ?? props.project?.name ?? '')
/** Project pages and document pages both carry the versions, in different places. */
const versionSwitcher = computed(() => {
  const key = props.project?.key ?? props.document?.project.slug

  if (!key) {
    return null
  }

  return {
    project: key,
    versions: props.project?.versions ?? props.document?.project.versions ?? [],
    current: props.project?.version ?? props.document?.project.version ?? undefined,
  }
})

const currentPageLabel = computed(() => props.document?.title ?? 'Overview')

const docsSheetOpen = ref(false)
</script>

<template>
  <UContainer class="px-4 sm:px-7">
    <UPage :ui="{ root: 'lg:gap-12' }">
      <template #left>
        <UPageAside :ui="{ root: 'pt-7 pb-16', container: 'flex flex-col gap-6' }">
          <VersionSwitcher
            v-if="versionSwitcher"
            v-bind="versionSwitcher"
          />

          <DocsTree :nav="nav" />
        </UPageAside>
      </template>

      <!-- Mobile sticky docs bar -->
      <div
        class="-mx-4 flex items-center gap-2 border-b border-neutral-900 bg-neutral-950/95 px-4 py-2.5 sm:-mx-7 sm:px-7 lg:hidden"
      >
        <UButton
          block
          variant="outline"
          color="neutral"
          trailing-icon="i-lucide-chevron-down"
          :label="currentPageLabel"
          class="h-11 min-w-0 flex-1 rounded-full bg-neutral-900 px-4 font-sans text-sm font-medium text-neutral-50 ring-neutral-800"
          :ui="{ label: 'min-w-0', trailingIcon: 'text-neutral-500' }"
          @click="docsSheetOpen = true"
        />

        <div
          v-if="versionSwitcher"
          class="w-32 shrink-0"
        >
          <VersionSwitcher v-bind="versionSwitcher" />
        </div>
      </div>

      <div class="flex flex-col gap-10 pb-16 lg:pt-7">
        <ProjectHero
          v-if="project"
          :project="project"
          class="mt-6 lg:mt-0"
        />

        <div class="grid grid-cols-1 gap-x-12 gap-y-6 xl:grid-cols-[minmax(0,44rem)_minmax(0,1fr)]">
          <div class="min-w-0">
            <slot />
          </div>

          <div
            v-if="toc.length"
            class="-order-1 min-w-0 xl:order-0"
          >
            <!--
              UContentToc swaps its collapsible for the full list at lg, but the
              rail only exists from xl, so the container's selectors keep the
              collapsible until then.
            -->
            <DocsToc
              :links="toc"
              :ui="{
                root: '-mx-4 px-4 sm:-mx-7 sm:px-7 lg:mx-0 lg:bg-default/75 lg:px-0 xl:static xl:max-h-none xl:overflow-visible xl:bg-[initial]',
                container: [
                  'py-3 sm:py-3 lg:border-b lg:py-3 xl:border-0 xl:p-0',
                  'lg:[&>button[data-slot=trigger]]:flex xl:[&>button[data-slot=trigger]]:hidden',
                  'lg:[&>[data-slot=content][data-state]]:block xl:[&>[data-slot=content][data-state]]:hidden',
                  'lg:[&>p[data-slot=trigger]]:hidden xl:[&>p[data-slot=trigger]]:flex',
                  'lg:[&>div[data-slot=content]:not([data-state])]:hidden xl:[&>div[data-slot=content]:not([data-state])]:flex',
                ].join(' '),
                title: 'font-mono text-xs font-normal tracking-[.14em] text-neutral-500 uppercase',
                trailingIcon: 'lg:block xl:hidden',
                trigger: 'mt-0 py-0',
                list: 'mt-2.5 flex flex-col gap-2.5',
                link: 'min-w-0 py-0 text-sm text-neutral-400 hover:text-neutral-50',
                linkText: 'min-w-0 truncate',
              }"
            />
          </div>
        </div>
      </div>
    </UPage>
  </UContainer>

  <MobileDocsSheet
    v-model:open="docsSheetOpen"
    :title="`${docsTitle} docs`"
    :nav="nav"
  />
</template>
