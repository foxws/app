<script setup lang="ts">
import DocsSurround from '@/components/Ui/DocsSurround.vue'
import ProseContent from '@/components/Ui/ProseContent.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import DocsLayout from '@/layouts/DocsLayout.vue'
import type { DocsDocument } from '@/types'
import { Head } from '@inertiajs/vue3'

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

  <div class="flex flex-col gap-6">
    <UButton
      :to="document.project.href"
      icon="i-lucide-arrow-left"
      variant="link"
      color="neutral"
      :ui="{
        base: 'self-start p-0 font-mono text-xs tracking-wider text-neutral-500 uppercase hover:text-neutral-300',
      }"
    >
      {{ document.project.name }}
    </UButton>

    <h1 class="font-sans text-4xl leading-[1.05] font-semibold tracking-tight text-neutral-50 sm:text-5xl">
      {{ document.title }}
    </h1>

    <ProseContent :html="document.html" />

    <DocsSurround :surround="document.surround" />
  </div>
</template>
