<script setup lang="ts">
import DocsTree from '@/components/Ui/DocsTree.vue'
import InstallCommand from '@/components/Ui/InstallCommand.vue'
import MobileDocsSheet from '@/components/Ui/MobileDocsSheet.vue'
import ProjectHero from '@/components/Ui/ProjectHero.vue'
import ProseContent from '@/components/Ui/ProseContent.vue'
import VersionSwitcher from '@/components/Ui/VersionSwitcher.vue'
import { Head } from '@inertiajs/vue3'
// Not auto-registered outside a @nuxt/content app (see vite.config.ts's `content: true`).
import UContentToc from '@nuxt/ui/components/content/ContentToc.vue'
import { ref } from 'vue'

interface Project {
  key: string
  name: string
  slug: string
  eyebrow: string
  lead: string
  install: string
  overview: { html: string; toc: { id: string; text: string; children: { id: string; text: string }[] }[] } | null
  nav: { title: string; path?: string; children?: { title: string; path: string }[] }[]
  versions: { name: string; is_default: boolean }[]
  github: string | null
}

defineProps<{
  project: Project
}>()

const docsSheetOpen = ref(false)
const onThisPageSheetOpen = ref(false)
</script>

<template>
  <Head :title="project.name" />

  <div class="lg:grid lg:grid-cols-[224px_1fr_232px]">
    <!-- Desktop docs tree -->
    <aside class="hidden border-r border-neutral-900 p-5.5 lg:block">
      <DocsTree :nav="project.nav" />
    </aside>

    <main class="min-w-0">
      <!-- Mobile sticky docs/on-this-page bar -->
      <div class="grid grid-cols-2 gap-2 border-b border-neutral-900 bg-neutral-950/95 px-4 py-2.5 lg:hidden">
        <button
          type="button"
          class="flex items-center justify-between rounded-lg border border-neutral-800 bg-neutral-900 px-3.25 py-2.5"
          @click="docsSheetOpen = true"
        >
          <span class="font-sans text-xs font-medium text-neutral-50">Docs</span>
          <span class="font-mono text-[10px] text-neutral-500">▾</span>
        </button>
        <button
          type="button"
          class="flex items-center justify-between rounded-lg border border-neutral-800 bg-neutral-900 px-3.25 py-2.5"
          @click="onThisPageSheetOpen = true"
        >
          <span class="font-sans text-xs font-medium text-neutral-50">On this page</span>
          <span class="font-mono text-[10px] text-neutral-500">▾</span>
        </button>
      </div>

      <div class="flex flex-col gap-5.5 p-4 sm:p-5.5">
        <ProjectHero
          :eyebrow="project.eyebrow"
          :title="project.name"
          :lead="project.lead"
        />

        <!-- Mobile-only version switcher, in place of the desktop right rail -->
        <div class="lg:hidden">
          <VersionSwitcher :versions="project.versions" />
        </div>

        <InstallCommand :command="project.install" />

        <ProseContent
          v-if="project.overview"
          :html="project.overview.html"
        />
      </div>
    </main>

    <!-- Desktop right rail: version switcher, GitHub link, then on-this-page -->
    <aside class="hidden flex-col gap-5.5 border-l border-neutral-900 p-5.5 lg:flex">
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

      <UContentToc
        v-if="project.overview?.toc.length"
        :links="project.overview.toc"
      />
    </aside>
  </div>

  <MobileDocsSheet
    v-model:open="docsSheetOpen"
    :title="`${project.name} docs`"
    :nav="project.nav"
  />

  <USlideover
    v-model:open="onThisPageSheetOpen"
    side="bottom"
    :ui="{ content: 'top-16 rounded-t-2xl border-t border-neutral-700 bg-neutral-900' }"
  >
    <template #content>
      <div class="flex items-center justify-between border-b border-neutral-800 px-4.5 py-3">
        <span class="font-sans text-[15px] font-semibold text-neutral-50">On this page</span>
        <button
          type="button"
          class="rounded-full bg-neutral-800 px-2.5 py-1 font-mono text-[10px] text-neutral-300"
          @click="onThisPageSheetOpen = false"
        >
          CLOSE
        </button>
      </div>
      <div class="p-4.5">
        <UContentToc
          v-if="project.overview?.toc.length"
          :links="project.overview.toc"
        />
      </div>
    </template>
  </USlideover>
</template>
