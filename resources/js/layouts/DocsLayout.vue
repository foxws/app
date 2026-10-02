<script setup lang="ts">
import DocsToc from '@/components/Ui/DocsToc.vue'
import DocsTree from '@/components/Ui/DocsTree.vue'
import MobileDocsSheet from '@/components/Ui/MobileDocsSheet.vue'
import PackageInfo from '@/components/Ui/PackageInfo.vue'
import UsedByList from '@/components/Ui/UsedByList.vue'
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
    <UPage>
      <template #left>
        <UPageAside>
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
          class="rounded-lg border border-neutral-800 bg-neutral-900 py-2.5 font-sans text-xs font-medium text-neutral-50"
          :ui="{ label: 'min-w-0', trailingIcon: 'text-neutral-500' }"
          @click="docsSheetOpen = true"
        />
      </div>

      <slot />

      <template
        v-if="project || toc.length"
        #right
      >
        <div
          v-if="project"
          class="hidden flex-col gap-5.5 py-8 lg:sticky lg:top-(--ui-header-height) lg:flex lg:max-h-[calc(100vh-var(--ui-header-height))] lg:overflow-x-hidden lg:overflow-y-auto"
        >
          <VersionSwitcher
            :project="project.key"
            :versions="project.versions"
            :current="project.version ?? undefined"
          />

          <UButton
            v-if="project.source"
            :to="project.source"
            target="_blank"
            variant="outline"
            color="neutral"
            class="justify-center rounded-lg py-2.5 font-sans text-sm font-medium"
          >
            Source ↗
          </UButton>

          <PackageInfo
            v-if="project.package"
            :info="project.package"
          />

          <DocsToc
            v-if="toc.length"
            :links="toc"
            :ui="{
              root: 'static mx-0 max-h-none overflow-visible px-0 sm:mx-0 sm:px-0',
              container: 'p-0 sm:p-0 lg:min-h-0 lg:p-0',
              link: 'min-w-0',
              linkText: 'min-w-0 truncate',
            }"
          />

          <UsedByList
            v-if="project.used_by.length"
            :projects="project.used_by"
          />
        </div>

        <!--
          Document pages have no desktop-only rail above, so this is their
          only copy of the TOC — it must show at every width. Project pages
          already show one at lg+ inside the rail above; this second copy
          only needs to fill the mobile gap below lg.
        -->
        <DocsToc
          v-if="toc.length"
          :class="project ? 'lg:hidden' : undefined"
          :links="toc"
          :ui="{ link: 'min-w-0', linkText: 'min-w-0 truncate' }"
        />
      </template>
    </UPage>
  </UContainer>

  <MobileDocsSheet
    v-model:open="docsSheetOpen"
    :title="`${docsTitle} docs`"
    :nav="nav"
  />
</template>
