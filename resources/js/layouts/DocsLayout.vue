<script setup lang="ts">
import DocsToc from '@/components/Ui/DocsToc.vue'
import DocsTree from '@/components/Ui/DocsTree.vue'
import MobileDocsSheet from '@/components/Ui/MobileDocsSheet.vue'
import PackageInfo from '@/components/Ui/PackageInfo.vue'
import UsedByCard from '@/components/Ui/UsedByCard.vue'
import VersionSwitcher from '@/components/Ui/VersionSwitcher.vue'
import type { DocsNavItem, DocsPackageInfo, DocsTocItem, DocsUsedBy } from '@/types'
import { computed, ref } from 'vue'

/**
 * Inertia spreads the full page props onto every layout in the chain, so
 * `document`/`project` below arrive automatically — pages don't pass them in.
 * Only one of the two is ever set, depending on which page rendered.
 */
const props = defineProps<{
  document?: { title: string; project: { name: string }; nav: DocsNavItem[]; toc: DocsTocItem[] }
  project?: {
    name: string
    nav: DocsNavItem[]
    versions: { name: string; is_default: boolean }[]
    github: string | null
    overview: { toc: DocsTocItem[] } | null
    package: DocsPackageInfo | null
    used_by: DocsUsedBy | null
  }
}>()

const nav = computed(() => props.document?.nav ?? props.project?.nav ?? [])
const toc = computed(() => props.document?.toc ?? props.project?.overview?.toc ?? [])
const docsTitle = computed(() => props.document?.project.name ?? props.project?.name ?? '')
const currentPageLabel = computed(() => props.document?.title ?? 'Overview')

const docsSheetOpen = ref(false)
const tocSheetOpen = ref(false)
</script>

<template>
  <UContainer class="px-4 sm:px-7">
    <UPage>
      <template #left>
        <UPageAside>
          <DocsTree :nav="nav" />
        </UPageAside>
      </template>

      <!-- Mobile sticky docs/on-this-page bar -->
      <div
        class="-mx-4 grid gap-2 border-b border-neutral-900 bg-neutral-950/95 px-4 py-2.5 sm:-mx-7 sm:px-7 lg:hidden"
        :class="toc.length ? 'grid-cols-2' : 'grid-cols-1'"
      >
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

        <UButton
          v-if="toc.length"
          block
          variant="outline"
          color="neutral"
          trailing-icon="i-lucide-chevron-down"
          label="On this page"
          class="rounded-lg border border-neutral-800 bg-neutral-900 py-2.5 font-sans text-xs font-medium text-neutral-50"
          :ui="{ trailingIcon: 'text-neutral-500' }"
          @click="tocSheetOpen = true"
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
          <VersionSwitcher :versions="project.versions" />

          <UButton
            v-if="project.github"
            :to="`https://github.com/${project.github}`"
            target="_blank"
            variant="outline"
            color="neutral"
            class="justify-center rounded-lg py-2.5 font-sans text-[13px] font-medium"
          >
            GitHub ↗
          </UButton>

          <PackageInfo
            v-if="project.package"
            :info="project.package"
          />

          <DocsToc
            v-if="toc.length"
            :links="toc"
            :ui="{
              root: 'static mx-0 px-0 sm:mx-0 sm:px-0 max-h-none overflow-visible',
              container: 'p-0 sm:p-0 lg:p-0 lg:min-h-0',
              link: 'min-w-0',
              linkText: 'min-w-0 truncate',
            }"
          />

          <UsedByCard
            v-if="project.used_by"
            :name="project.used_by.name"
            :desc="project.used_by.desc"
            :href="project.used_by.href"
          />
        </div>

        <DocsToc
          v-else-if="toc.length"
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

  <USlideover
    v-model:open="tocSheetOpen"
    side="bottom"
    title="On this page"
    :close="{
      label: 'Close',
      icon: '',
      color: 'neutral',
      variant: 'soft',
      class:
        'rounded-full bg-neutral-800 px-2.5 py-1 font-mono text-[10px] text-neutral-300 uppercase hover:bg-neutral-700',
    }"
    :ui="{
      content: 'max-h-[70vh] rounded-t-2xl border-t border-neutral-700 bg-neutral-900',
      header: 'min-h-0 border-b border-neutral-800 px-4.5 py-3 sm:px-4.5 sm:py-3',
      title: 'font-sans text-[15px] font-semibold text-neutral-50',
      body: 'overflow-y-auto p-4.5 sm:p-4.5',
    }"
  >
    <template #body>
      <DocsToc
        :links="toc"
        :ui="{
          root: 'static mx-0 px-0 sm:mx-0 sm:px-0 max-h-none overflow-visible',
          container: 'p-0 sm:p-0 lg:p-0',
          trigger: 'hidden',
          link: 'min-w-0',
          linkText: 'min-w-0 truncate',
        }"
        @navigate="tocSheetOpen = false"
      />
    </template>
  </USlideover>
</template>
