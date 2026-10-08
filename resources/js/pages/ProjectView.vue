<script setup lang="ts">
import DocsSurround from '@/components/Ui/DocsSurround.vue'
import ProseContent from '@/components/Ui/ProseContent.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import DocsLayout from '@/layouts/DocsLayout.vue'
import type { DocsProject } from '@/types'
import { Head } from '@inertiajs/vue3'

// The package hero lives in DocsLayout, so it can span the TOC column too.
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

  <div class="flex flex-col gap-6">
    <ProseContent
      v-if="project.overview"
      :html="project.overview.html"
    />

    <DocsSurround :surround="project.surround" />
  </div>
</template>
