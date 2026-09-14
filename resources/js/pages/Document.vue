<script setup lang="ts">
import ProseContent from '@/components/Ui/ProseContent.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import DocsLayout from '@/layouts/DocsLayout.vue'
import { Head } from '@inertiajs/vue3'
import type { ContentSurroundLink } from '@nuxt/ui/components/content/ContentSurround.vue'
import UContentSurround from '@nuxt/ui/components/content/ContentSurround.vue'
import type { DocsNavItem, DocsSurroundLink, DocsTocItem } from '@/types'

interface DocumentPage {
  project: { name: string; slug: string }
  title: string
  html: string
  toc: DocsTocItem[]
  nav: DocsNavItem[]
  surround: (DocsSurroundLink | null)[]
}

defineOptions({ layout: [AppLayout, DocsLayout] })

defineProps<{
  document: DocumentPage
}>()
</script>

<template>
  <Head :title="`${document.title} - ${document.project.name}`" />

  <div class="flex flex-col gap-5.5 py-4 sm:py-5.5">
    <ULink
      :to="`/${document.project.slug}`"
      class="font-mono text-[11px] text-neutral-500 hover:text-neutral-300"
    >
      ← {{ document.project.name }}
    </ULink>

    <h1 class="font-sans text-3xl font-semibold tracking-tight text-neutral-50">{{ document.title }}</h1>

    <ProseContent :html="document.html" />

    <!--
      UContentSurround's own template guards each slot with `v-if="link"` and
      renders a blank placeholder for a missing prev/next — its .d.ts just doesn't
      declare that, so the null entries here need a cast.
    -->
    <UContentSurround :surround="document.surround as unknown as ContentSurroundLink[]" />
  </div>
</template>
