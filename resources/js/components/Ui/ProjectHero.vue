<script setup lang="ts">
import { useMonogram } from '@/composables/monogram'
import InstallCommand from '@/components/Ui/InstallCommand.vue'
import PackageInfo from '@/components/Ui/PackageInfo.vue'
import type { DocsProject } from '@/types'
import { computed } from 'vue'

const props = defineProps<{
  project: DocsProject
}>()

const { formatMonogram } = useMonogram()

const monogram = computed(() => formatMonogram(props.project.name))
</script>

<template>
  <section
    aria-label="Package"
    class="flex flex-col gap-6 rounded-4xl p-6 ring-1 ring-neutral-800 bg-package-panel ring-inset sm:p-10 lg:p-12"
  >
    <div class="flex flex-wrap items-center gap-x-5 gap-y-4">
      <span
        aria-hidden="true"
        class="flex size-18 shrink-0 items-center justify-center rounded-[1.25rem] bg-gold-400 font-mono text-lg font-bold tracking-wider text-neutral-950"
      >
        {{ monogram }}
      </span>

      <div class="flex min-w-0 flex-[1_1_17.5rem] flex-col gap-2.5">
        <p
          v-if="project.eyebrow"
          class="font-mono text-xs tracking-[.18em] text-gold-400 uppercase"
        >
          {{ project.eyebrow }}
        </p>

        <h1 class="font-sans text-4xl leading-[.95] font-bold tracking-tight text-neutral-50 sm:text-5xl lg:text-6xl">
          {{ project.name }}
        </h1>
      </div>
    </div>

    <p
      v-if="project.lead"
      class="max-w-160 font-sans text-base leading-relaxed text-pretty text-neutral-300 sm:text-lg"
    >
      {{ project.lead }}
    </p>

    <div
      v-if="project.install || project.get_started || project.source"
      class="flex flex-wrap items-center gap-3"
    >
      <InstallCommand
        v-if="project.install"
        :command="project.install"
      />

      <UButton
        v-if="project.get_started"
        :to="project.get_started"
        label="Get started"
        color="neutral"
        variant="solid"
        class="h-12 rounded-full bg-gold-400 px-5.5 font-sans text-sm font-semibold text-neutral-950 hover:bg-gold-400 hover:brightness-112"
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

    <PackageInfo
      v-if="project.package || project.downloads"
      :info="project.package ?? {}"
      :downloads="project.downloads"
      class="mt-2 border-t border-neutral-50/10 pt-6"
    />
  </section>
</template>
