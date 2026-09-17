<script setup lang="ts">
import InstallCommand from '@/components/Ui/InstallCommand.vue'
import PackageInfo from '@/components/Ui/PackageInfo.vue'
import ProjectHero from '@/components/Ui/ProjectHero.vue'
import ProseContent from '@/components/Ui/ProseContent.vue'
import VersionSwitcher from '@/components/Ui/VersionSwitcher.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import DocsLayout from '@/layouts/DocsLayout.vue'
import type { DocsProject } from '@/types'
import { Head } from '@inertiajs/vue3'

defineOptions({ layout: [AppLayout, DocsLayout] })

defineProps<{
  project: DocsProject
}>()
</script>

<template>
  <Head :title="project.name">
    <meta
      head-key="description"
      name="description"
      :content="project.lead"
    />
  </Head>

  <div class="flex flex-col gap-5.5 py-4 sm:py-5.5">
    <ProjectHero
      :eyebrow="project.eyebrow"
      :title="project.name"
      :lead="project.lead"
    />

    <!-- Mobile-only version switcher, in place of the desktop right rail -->
    <div class="lg:hidden">
      <VersionSwitcher
        :project="project.key"
        :versions="project.versions"
        :current="project.version ?? undefined"
      />
    </div>

    <PackageInfo
      v-if="project.package"
      :info="project.package"
      variant="grid"
      class="lg:hidden"
    />

    <InstallCommand :command="project.install" />

    <UButton
      v-if="project.get_started"
      :to="project.get_started"
      block
      size="lg"
      class="rounded-lg bg-identity-500 py-3 font-sans text-base font-semibold text-neutral-950 hover:bg-identity-400 lg:hidden"
    >
      Get started
    </UButton>

    <UButton
      v-if="project.source"
      :to="project.source"
      target="_blank"
      block
      variant="outline"
      color="neutral"
      class="justify-center rounded-lg py-2.5 font-sans text-sm font-medium lg:hidden"
    >
      Source ↗
    </UButton>

    <ProseContent
      v-if="project.overview"
      :html="project.overview.html"
    />
  </div>
</template>
