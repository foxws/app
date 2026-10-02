<script setup lang="ts">
import PackageCard from '@/components/Ui/PackageCard.vue'
import SideProjectRow from '@/components/Ui/SideProjectRow.vue'
import type { DocsPackageGroup, DocsSideProjectSummary } from '@/types'
import { Head } from '@inertiajs/vue3'

defineProps<{
  packageGroups: DocsPackageGroup[]
  sideProjects: DocsSideProjectSummary[]
}>()
</script>

<template>
  <Head title="Home">
    <meta
      head-key="description"
      name="description"
      content="I build Laravel packages and tools for video streaming, documentation, domain-driven design, and Podman-based deployments."
    />
  </Head>

  <UPageHero
    headline="Laravel · Linux · AI"
    title="The hard parts, already solved."
    description="I like building all kinds of solutions to all kinds of problems — video streaming, documentation generators, domain-driven design, or deep system integration with things like Podman."
    :ui="{
      root: 'border-b border-neutral-900',
      container: 'px-4 py-16 sm:px-7 sm:py-19 lg:flex lg:flex-col lg:px-7 lg:py-19',
      wrapper: 'text-left',
      header: 'flex flex-col gap-6',
      headline: 'mb-0 block',
      title: 'mt-0 max-w-4xl font-sans text-4xl leading-[.98] font-semibold text-neutral-50 sm:text-7xl',
      description: 'mt-0 max-w-2xl font-sans text-base leading-relaxed text-neutral-400 sm:text-lg',
    }"
  />

  <div class="divide-y divide-neutral-900">
    <UContainer
      id="packages"
      as="section"
      class="flex flex-col gap-8 px-4 py-8 sm:px-7"
    >
      <h2 class="sr-only">Packages</h2>

      <div
        v-for="group in packageGroups"
        :key="group.name ?? 'ungrouped'"
        class="flex flex-col gap-3.5"
      >
        <!-- Ungrouped packages only get a label when there are groups to set them apart from. -->
        <h3
          v-if="group.name || packageGroups.length > 1"
          class="font-mono text-xs tracking-wider text-neutral-500 uppercase"
        >
          {{ group.name ?? 'More' }}
        </h3>

        <div class="grid grid-cols-1 gap-3.5 sm:grid-cols-2">
          <PackageCard
            v-for="pkg in group.packages"
            :key="pkg.slug"
            :name="pkg.name"
            :slug="pkg.slug"
            :role="pkg.role"
            :desc="pkg.desc"
            :version="pkg.version"
            :href="`/${pkg.path}`"
            :downloads="pkg.downloads"
          />
        </div>
      </div>
    </UContainer>

    <UContainer
      v-if="sideProjects.length"
      as="section"
      class="px-4 py-8 sm:px-7"
    >
      <UPageCard
        variant="naked"
        :ui="{
          container: 'p-0 sm:p-0',
          wrapper: 'items-stretch gap-4',
          header: 'mb-0',
          footer: 'mt-0 pt-0',
        }"
      >
        <template #header>
          <div class="flex flex-col gap-3.5 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex flex-col gap-1.5">
              <h2 class="font-sans text-xl font-semibold tracking-tight text-neutral-50">Side projects</h2>
              <p class="max-w-md font-sans text-sm text-neutral-400">
                Not part of the core. Experiments, templates and notes I keep around because they're useful.
              </p>
            </div>
            <UButton
              label="Personal profile"
              to="https://github.com/francoism90"
              target="_blank"
              variant="link"
              color="neutral"
              trailing-icon="i-lucide-arrow-up-right"
              class="hidden shrink-0 self-start px-0 sm:inline-flex"
            />
          </div>
        </template>

        <template #body>
          <UPageList divide>
            <SideProjectRow
              v-for="project in sideProjects"
              :key="project.slug"
              :name="project.name"
              :type="project.type"
              :desc="project.desc"
              :status="project.status"
              :href="project.href"
            />
          </UPageList>
        </template>

        <template #footer>
          <UButton
            label="Personal profile"
            to="https://github.com/francoism90"
            target="_blank"
            variant="outline"
            size="sm"
            color="neutral"
            trailing-icon="i-lucide-arrow-up-right"
            class="justify-center rounded-lg sm:hidden"
          />
        </template>
      </UPageCard>
    </UContainer>

    <UContainer
      as="section"
      class="px-4 py-8 sm:px-7"
    >
      <UPageCard
        variant="naked"
        :ui="{
          container: 'p-0 sm:p-0',
          wrapper: 'items-stretch gap-2.75',
          header: 'mb-0',
          description: 'font-sans text-sm text-neutral-400',
          footer: 'mt-0 pt-0',
        }"
      >
        <template #header>
          <h2 class="font-sans text-xl font-semibold tracking-tight text-neutral-50">Let's connect</h2>
        </template>

        <template #description
          >I'm on LinkedIn — feel free to connect if you want to talk Laravel, Linux, or anything in between.</template
        >

        <template #footer>
          <UButton
            label="Connect on LinkedIn"
            to="https://www.linkedin.com/in/francoismenning/"
            trailing-icon="i-lucide-user-round-plus"
            size="sm"
          />
        </template>
      </UPageCard>
    </UContainer>
  </div>
</template>
