<script setup lang="ts">
import FeaturedProject from '@/components/Ui/FeaturedProject.vue'
import ProseContent from '@/components/Ui/ProseContent.vue'
import type { DocsProjectShowcase, DocsProjectSummary } from '@/types'
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps<{
  project: DocsProjectShowcase
  moreProjects: DocsProjectSummary[]
}>()

const docsIsExternal = computed(() => props.project.docs?.startsWith('http') ?? false)
</script>

<template>
  <Head :title="project.name">
    <meta
      head-key="description"
      name="description"
      :content="project.desc"
    />
  </Head>

  <section class="[--glow-x:76%] bg-page-glow">
    <UContainer
      :class="[
        'grid items-center gap-x-14 gap-y-10 px-4 py-12 sm:px-7 sm:py-16 lg:py-20',
        project.image ? 'lg:grid-cols-[minmax(0,1fr)_minmax(0,1.15fr)]' : undefined,
      ]"
    >
      <div class="flex min-w-0 flex-col gap-5">
        <h1
          class="font-sans text-6xl leading-[.9] font-bold tracking-[-.04em] break-words text-neutral-50 sm:text-8xl lg:text-9xl"
        >
          {{ project.name }}
        </h1>

        <p
          v-if="project.desc"
          class="max-w-120 font-sans text-lg leading-normal text-pretty text-neutral-300 sm:text-xl"
        >
          {{ project.desc }}
        </p>

        <div
          v-if="project.docs || project.source"
          class="mt-2 flex flex-wrap items-center gap-3"
        >
          <UButton
            v-if="project.docs"
            :to="project.docs"
            :target="docsIsExternal ? '_blank' : undefined"
            label="Read the docs"
            trailing-icon="i-lucide-arrow-right"
            color="neutral"
            variant="solid"
            class="h-12 rounded-full bg-neutral-50 px-5.5 font-sans text-sm font-semibold text-neutral-950 hover:bg-neutral-200"
          />

          <UButton
            v-if="project.source"
            :to="project.source"
            target="_blank"
            label="Source"
            trailing-icon="i-lucide-arrow-up-right"
            color="neutral"
            variant="outline"
            class="h-12 rounded-full px-5.5 font-sans text-sm font-medium text-neutral-50 ring-neutral-50/28 hover:bg-neutral-50/8"
          />
        </div>
      </div>

      <img
        v-if="project.image"
        :src="project.image"
        :alt="`${project.name} screenshot`"
        class="aspect-16/10 w-full min-w-0 rounded-3xl bg-neutral-900 object-cover ring-1 ring-neutral-800"
      />
    </UContainer>

    <UContainer
      v-if="project.introduction || project.technologies.length"
      class="px-4 pt-4 pb-20 sm:px-7"
    >
      <div class="mx-auto flex max-w-160 flex-col gap-7">
        <ProseContent
          v-if="project.introduction"
          :html="project.introduction"
          lead
        />

        <div
          v-if="project.technologies.length"
          class="mt-3 flex flex-wrap items-center gap-x-6 gap-y-3 border-t border-neutral-800 pt-6"
        >
          <h2 class="font-mono text-xs tracking-[.14em] text-neutral-500 uppercase">Built with</h2>

          <ul class="flex flex-wrap gap-2">
            <li
              v-for="technology in project.technologies"
              :key="technology"
            >
              <UBadge
                :label="technology"
                color="neutral"
                variant="soft"
                class="rounded-md bg-neutral-900 px-2.5 py-1 font-mono text-[13px] leading-5 font-normal text-neutral-200"
              />
            </li>
          </ul>
        </div>
      </div>
    </UContainer>
  </section>

  <section
    v-if="moreProjects.length"
    class="border-t border-neutral-900"
  >
    <UContainer class="flex flex-col gap-6 px-4 pt-12 pb-16 sm:px-7">
      <h2 class="font-mono text-xs tracking-[.14em] text-neutral-500 uppercase">More projects</h2>

      <div class="grid grid-cols-1 gap-x-8 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
        <FeaturedProject
          v-for="other in moreProjects"
          :key="other.slug"
          :name="other.name"
          :desc="other.desc"
          :href="other.href"
        />
      </div>
    </UContainer>
  </section>
</template>
