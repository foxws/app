<script setup lang="ts">
import ProseContent from '@/components/Ui/ProseContent.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import DocsLayout from '@/layouts/DocsLayout.vue'
import { Head } from '@inertiajs/vue3'
import type { ContentSurroundLink } from '@nuxt/ui/components/content/ContentSurround.vue'
import UContentSurround from '@nuxt/ui/components/content/ContentSurround.vue'
import type { DocsDocument } from '@/types'

defineOptions({ layout: [AppLayout, DocsLayout] })

defineProps<{
  document: DocsDocument
}>()
</script>

<template>
  <Head :title="`${document.title} - ${document.project.name}`">
    <meta
      head-key="description"
      name="description"
      :content="document.description"
    />
  </Head>

  <div class="flex flex-col gap-5.5 py-4 sm:py-5.5">
    <UButton
      :to="document.project.href"
      icon="i-lucide-arrow-left"
      variant="link"
      color="neutral"
      :ui="{ base: 'p-0 font-mono text-xs text-neutral-500 hover:text-neutral-300' }"
    >
      {{ document.project.name }}
    </UButton>

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
