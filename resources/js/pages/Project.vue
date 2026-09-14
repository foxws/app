<script setup lang="ts">
import CodeBlock from '@/components/Ui/CodeBlock.vue'
import DocsTree from '@/components/Ui/DocsTree.vue'
import FeatureTile from '@/components/Ui/FeatureTile.vue'
import InstallCommand from '@/components/Ui/InstallCommand.vue'
import MobileDocsSheet from '@/components/Ui/MobileDocsSheet.vue'
import OnThisPage from '@/components/Ui/OnThisPage.vue'
import PackageMeta from '@/components/Ui/PackageMeta.vue'
import PrevNextNav from '@/components/Ui/PrevNextNav.vue'
import ProjectHero from '@/components/Ui/ProjectHero.vue'
import { Head } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

interface Project {
  key: string
  name: string
  slug: string
  flagship: boolean
  role: string | null
  eyebrow: string
  title_lines: [string, string]
  lead: string
  install: string
  meta: { k: string; v: string }[]
  nav: { group: string; items: string[] }[]
  on_this_page: string[]
  features: { t: string; d: string }[]
  code: { filename: string; language: string; code: string } | null
}

const props = defineProps<{
  project: Project
}>()

const prevLabel = computed(() => props.project.nav[0]?.items[0])
const nextLabel = computed(() => props.project.nav[0]?.items[1] ?? props.project.nav[1]?.items[0])

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
          :title-lines="project.title_lines"
          :lead="project.lead"
        />

        <!-- Mobile-only inline meta grid, in place of the desktop right rail -->
        <div class="grid grid-cols-2 gap-px overflow-hidden rounded-lg border border-neutral-800 bg-neutral-900 lg:hidden">
          <div
            v-for="m in project.meta"
            :key="m.k"
            class="flex flex-col gap-0.5 bg-neutral-950 p-2.75"
          >
            <span class="font-mono text-[10px] tracking-wider text-neutral-500">{{ m.k }}</span>
            <span class="font-mono text-[13px] text-neutral-200">{{ m.v }}</span>
          </div>
        </div>

        <InstallCommand :command="project.install" />

        <div class="flex flex-col gap-2">
          <h2 class="font-sans text-xl font-semibold tracking-tight text-neutral-50 sm:text-[22px]">What it does</h2>
          <p class="max-w-xl font-sans text-[15px] leading-relaxed text-neutral-400">{{ project.lead }}</p>
        </div>

        <div class="grid grid-cols-1 gap-2.5 sm:grid-cols-2">
          <FeatureTile
            v-for="f in project.features"
            :key="f.t"
            :t="f.t"
            :d="f.d"
          />
        </div>

        <CodeBlock
          v-if="project.code"
          :filename="project.code.filename"
          :language="project.code.language"
          :code="project.code.code"
        />

        <div
          v-if="!project.flagship"
          class="rounded-lg border border-neutral-900 p-4"
        >
          <span class="block font-sans text-[13px] font-semibold text-neutral-50">Used by Stry</span>
          <span class="font-sans text-xs text-neutral-400">See it running in production →</span>
        </div>

        <PrevNextNav
          :prev="prevLabel"
          :next="nextLabel"
        />
      </div>
    </main>

    <!-- Desktop right rail: facts, then navigation -->
    <aside class="hidden flex-col gap-5.5 border-l border-neutral-900 p-5.5 lg:flex">
      <div class="flex flex-col gap-2">
        <UButton class="justify-center rounded-lg bg-identity-500 py-2.5 font-sans text-[13px] font-semibold text-neutral-950 hover:bg-identity-400">
          Get started
        </UButton>
        <UButton
          :to="`https://github.com/${project.slug}`"
          target="_blank"
          variant="outline"
          color="neutral"
          class="justify-center rounded-lg py-2.5 font-sans text-[13px] font-medium"
        >
          GitHub ↗
        </UButton>
      </div>

      <PackageMeta :meta="project.meta" />
      <OnThisPage :sections="project.on_this_page" />

      <div
        v-if="!project.flagship"
        class="rounded-lg border border-neutral-900 p-3.5"
      >
        <span class="block font-sans text-xs font-semibold text-neutral-50">Used by Stry</span>
        <span class="font-sans text-[11px] text-neutral-400">See it running in production →</span>
      </div>
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
        <OnThisPage :sections="project.on_this_page" />
      </div>
    </template>
  </USlideover>
</template>
