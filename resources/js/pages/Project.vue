<script setup lang="ts">
import InstallCommand from '@/components/Ui/InstallCommand.vue'
import ProjectHero from '@/components/Ui/ProjectHero.vue'
import ProseContent from '@/components/Ui/ProseContent.vue'
import VersionSwitcher from '@/components/Ui/VersionSwitcher.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import DocsLayout from '@/layouts/DocsLayout.vue'
import type { DocsNavItem, DocsTocItem } from '@/types'
import { Head } from '@inertiajs/vue3'

interface Project {
  key: string
  name: string
  slug: string
  eyebrow: string
  lead: string
  install: string
  overview: { html: string; toc: DocsTocItem[] } | null
  nav: DocsNavItem[]
  versions: { name: string; is_default: boolean }[]
  github: string | null
}

defineOptions({ layout: [AppLayout, DocsLayout] })

defineProps<{
  project: Project
}>()
</script>

<template>
  <Head :title="project.name" />

  <div class="flex flex-col gap-5.5 py-4 sm:py-5.5">
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
</template>
