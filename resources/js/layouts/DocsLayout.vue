<script setup lang="ts">
import DocsToc from '@/components/Ui/DocsToc.vue'
import DocsTree from '@/components/Ui/DocsTree.vue'
import MobileDocsSheet from '@/components/Ui/MobileDocsSheet.vue'
import ProjectHero from '@/components/Ui/ProjectHero.vue'
import SearchTrigger from '@/components/Ui/SearchTrigger.vue'
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
const currentPageLabel = computed(() => props.document?.title ?? 'Overview')

const docsSheetOpen = ref(false)
</script>

<template>
  <UContainer class="px-4 sm:px-7">
    <UPage :ui="{ root: 'lg:gap-12' }">
      <template #left>
        <UPageAside :ui="{ root: 'pt-7 pb-16', container: 'flex flex-col gap-6' }">
          <SearchTrigger
            label="Search docs"
            block
          />

          <VersionSwitcher
            v-if="project"
            :project="project.key"
            :versions="project.versions"
            :current="project.version ?? undefined"
          />

          <DocsTree :nav="nav" />
        </UPageAside>
      </template>

      <!-- Mobile sticky docs bar -->
      <div class="-mx-4 border-b border-neutral-900 bg-neutral-950/95 px-4 py-2.5 sm:-mx-7 sm:px-7 lg:hidden">
        <UButton
          block
          variant="outline"
          color="neutral"
          trailing-icon="i-lucide-chevron-down"
          :label="currentPageLabel"
          class="h-11 rounded-full bg-neutral-900 px-4 font-sans text-sm font-medium text-neutral-50 ring-neutral-800"
          :ui="{ label: 'min-w-0', trailingIcon: 'text-neutral-500' }"
          @click="docsSheetOpen = true"
        />
      </div>

      <div class="flex flex-col gap-10 pt-7 pb-16">
        <ProjectHero
          v-if="project"
          :project="project"
        />

        <div class="grid grid-cols-1 gap-x-12 gap-y-6 xl:grid-cols-[minmax(0,44rem)_minmax(0,1fr)]">
          <div class="min-w-0">
            <slot />
          </div>

          <div
            v-if="toc.length"
            class="-order-1 min-w-0 xl:order-0"
          >
            <DocsToc
              :links="toc"
              :ui="{
                root: '-mx-4 px-4 sm:-mx-7 sm:px-7 lg:mx-0 lg:px-0 xl:static xl:max-h-none xl:overflow-visible',
                container: 'pt-0 sm:pt-0 xl:p-0',
                title: 'font-mono text-xs font-normal tracking-[.14em] text-neutral-500 uppercase',
                trigger: 'py-0',
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
