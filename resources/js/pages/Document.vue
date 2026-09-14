<script setup lang="ts">
import DocsToc from '@/components/Ui/DocsToc.vue'
import DocsTree from '@/components/Ui/DocsTree.vue'
import MobileDocsSheet from '@/components/Ui/MobileDocsSheet.vue'
import ProseContent from '@/components/Ui/ProseContent.vue'
import { Head } from '@inertiajs/vue3'
import type { ContentSurroundLink } from '@nuxt/ui/components/content/ContentSurround.vue'
import UContentSurround from '@nuxt/ui/components/content/ContentSurround.vue'
import { ref } from 'vue'

interface DocumentPage {
  project: { name: string; slug: string }
  title: string
  html: string
  toc: { id: string; text: string; children: { id: string; text: string }[] }[]
  nav: { title: string; path?: string; children?: { title: string; path: string }[] }[]
  surround: ({ title: string; path: string } | null)[]
}

defineProps<{
  document: DocumentPage
}>()

const docsSheetOpen = ref(false)
const tocSheetOpen = ref(false)
</script>

<template>
  <Head :title="`${document.title} - ${document.project.name}`" />

  <UContainer class="px-4 sm:px-7">
    <UPage>
      <template #left>
        <UPageAside>
          <DocsTree :nav="document.nav" />
        </UPageAside>
      </template>

      <!-- Mobile sticky docs/on-this-page bar -->
      <div
        class="-mx-4 grid gap-2 border-b border-neutral-900 bg-neutral-950/95 px-4 py-2.5 sm:-mx-7 sm:px-7 lg:hidden"
        :class="document.toc.length ? 'grid-cols-2' : 'grid-cols-1'"
      >
        <button
          type="button"
          class="flex items-center justify-between rounded-lg border border-neutral-800 bg-neutral-900 px-3.25 py-2.5"
          @click="docsSheetOpen = true"
        >
          <span class="font-sans text-xs font-medium text-neutral-50">Docs</span>
          <span class="font-mono text-[10px] text-neutral-500">▾</span>
        </button>
        <button
          v-if="document.toc.length"
          type="button"
          class="flex items-center justify-between rounded-lg border border-neutral-800 bg-neutral-900 px-3.25 py-2.5"
          @click="tocSheetOpen = true"
        >
          <span class="font-sans text-xs font-medium text-neutral-50">On this page</span>
          <span class="font-mono text-[10px] text-neutral-500">▾</span>
        </button>
      </div>

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

      <template
        v-if="document.toc.length"
        #right
      >
        <DocsToc
          :links="document.toc"
          :ui="{ link: 'min-w-0', linkText: 'min-w-0 truncate' }"
        />
      </template>
    </UPage>
  </UContainer>

  <MobileDocsSheet
    v-model:open="docsSheetOpen"
    :title="`${document.project.name} docs`"
    :nav="document.nav"
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
          :links="document.toc"
          :ui="{ trigger: 'hidden', link: 'min-w-0', linkText: 'min-w-0 truncate' }"
          @navigate="tocSheetOpen = false"
        />
      </div>
    </template>
  </USlideover>
</template>
