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
        <button
          type="button"
          class="flex items-center justify-between rounded-lg border border-neutral-800 bg-neutral-900 px-3.25 py-2.5"
          @click="docsSheetOpen = true"
        >
          <span class="min-w-0 truncate font-sans text-xs font-medium text-neutral-50">{{ currentPageLabel }}</span>
          <span class="font-mono text-[10px] text-neutral-500">▾</span>
        </button>

        <button
          v-if="toc.length"
          type="button"
          class="flex items-center justify-between rounded-lg border border-neutral-800 bg-neutral-900 px-3.25 py-2.5"
          @click="tocSheetOpen = true"
        >
          <span class="font-sans text-xs font-medium text-neutral-50">On this page</span>
          <span class="font-mono text-[10px] text-neutral-500">▾</span>
        </button>
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
            :ui="{ link: 'min-w-0', linkText: 'min-w-0 truncate' }"
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
    :ui="{ content: 'max-h-[70vh] rounded-t-2xl border-t border-neutral-700 bg-neutral-900' }"
  >
    <template #content>
      <div class="flex items-center justify-between border-b border-neutral-800 px-4.5 py-3">
        <span class="font-sans text-[15px] font-semibold text-neutral-50">On this page</span>
        <button
          type="button"
          class="rounded-full bg-neutral-800 px-2.5 py-1 font-mono text-[10px] text-neutral-300 uppercase"
          @click="tocSheetOpen = false"
        >
          Close
        </button>
      </div>

      <div class="overflow-y-auto p-4.5">
        <DocsToc
          :links="toc"
          :ui="{ trigger: 'hidden', link: 'min-w-0', linkText: 'min-w-0 truncate' }"
          @navigate="tocSheetOpen = false"
        />
      </div>
    </template>
  </USlideover>
</template>
