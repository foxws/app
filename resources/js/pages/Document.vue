<script setup lang="ts">
import DocsTree from '@/components/Ui/DocsTree.vue'
import MobileDocsSheet from '@/components/Ui/MobileDocsSheet.vue'
import { Head } from '@inertiajs/vue3'
import { ref } from 'vue'

interface DocumentPage {
  project: { name: string; slug: string }
  title: string
  html: string
  toc: { id: string; text: string; children: { id: string; text: string }[] }[]
  nav: { title: string; path?: string; children?: { title: string; path: string }[] }[]
  surround: ({ title: string; path: string } | null)[]
}

const props = defineProps<{
  document: DocumentPage
}>()

const docsSheetOpen = ref(false)
const tocSheetOpen = ref(false)

const proseClass =
  'flex flex-col gap-4 font-sans text-[15px] leading-relaxed text-neutral-400 ' +
  '[&_h2]:mt-8 [&_h2]:text-xl [&_h2]:font-semibold [&_h2]:tracking-tight [&_h2]:text-neutral-50 ' +
  '[&_h3]:mt-6 [&_h3]:text-base [&_h3]:font-semibold [&_h3]:text-neutral-50 ' +
  '[&_a]:text-identity-500 [&_a]:underline [&_a]:underline-offset-2 ' +
  '[&_code]:rounded [&_code]:bg-neutral-900 [&_code]:px-1.5 [&_code]:py-0.5 [&_code]:font-mono [&_code]:text-[13px] [&_code]:text-neutral-200 ' +
  '[&_pre]:overflow-x-auto [&_pre]:rounded-lg [&_pre]:border [&_pre]:border-neutral-900 [&_pre]:bg-neutral-900 [&_pre]:p-3.5 [&_pre]:font-mono [&_pre]:text-[13px] [&_pre]:leading-[1.85] ' +
  '[&_pre_code]:bg-transparent [&_pre_code]:p-0 ' +
  '[&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5 [&_li]:my-1 ' +
  '[&_blockquote]:border-l [&_blockquote]:border-neutral-800 [&_blockquote]:pl-4 [&_blockquote]:text-neutral-500'
</script>

<template>
  <Head :title="`${document.title} - ${document.project.name}`" />

  <div class="lg:grid lg:grid-cols-[224px_1fr_232px]">
    <!-- Desktop docs tree -->
    <aside class="hidden border-r border-neutral-900 p-5.5 lg:block">
      <DocsTree :nav="document.nav" />
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
          @click="tocSheetOpen = true"
        >
          <span class="font-sans text-xs font-medium text-neutral-50">On this page</span>
          <span class="font-mono text-[10px] text-neutral-500">▾</span>
        </button>
      </div>

      <div class="flex flex-col gap-5.5 p-4 sm:p-5.5">
        <ULink
          :to="`/${document.project.slug}`"
          class="font-mono text-[11px] text-neutral-500 hover:text-neutral-300"
        >
          ← {{ document.project.name }}
        </ULink>

        <h1 class="font-sans text-3xl font-semibold tracking-tight text-neutral-50">{{ document.title }}</h1>

        <div
          :class="proseClass"
          v-html="document.html"
        />

        <UContentSurround :surround="document.surround" />
      </div>
    </main>

    <!-- Desktop right rail -->
    <aside class="hidden flex-col gap-5.5 border-l border-neutral-900 p-5.5 lg:flex">
      <UContentToc :links="document.toc" />
    </aside>
  </div>

  <MobileDocsSheet
    v-model:open="docsSheetOpen"
    :title="`${document.project.name} docs`"
    :nav="document.nav"
  />

  <USlideover
    v-model:open="tocSheetOpen"
    side="bottom"
    :ui="{ content: 'top-16 rounded-t-2xl border-t border-neutral-700 bg-neutral-900' }"
  >
    <template #content>
      <div class="flex items-center justify-between border-b border-neutral-800 px-4.5 py-3">
        <span class="font-sans text-[15px] font-semibold text-neutral-50">On this page</span>
        <button
          type="button"
          class="rounded-full bg-neutral-800 px-2.5 py-1 font-mono text-[10px] text-neutral-300"
          @click="tocSheetOpen = false"
        >
          CLOSE
        </button>
      </div>
      <div class="p-4.5">
        <UContentToc :links="document.toc" />
      </div>
    </template>
  </USlideover>
</template>
