<script setup lang="ts">
import PackageCard from '@/components/Ui/PackageCard.vue'
import type { DocsPackageSummary } from '@/types'
import { Head } from '@inertiajs/vue3'

defineProps<{
  packages: DocsPackageSummary[]
}>()

// Kept short and distinct from the hero copy below — search engines
// truncate meta descriptions around 155-160 characters.
const metaDescription =
  'I build Laravel packages and tools for video streaming, documentation, domain-driven design, and Podman-based deployments.'
</script>

<template>
  <Head title="Home">
    <meta
      head-key="description"
      name="description"
      :content="metaDescription"
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

  <UContainer
    as="section"
    class="flex flex-col gap-3.5 px-4 py-6 sm:px-7"
  >
    <div
      id="packages"
      class="grid grid-cols-1 gap-3.5 sm:grid-cols-2"
    >
      <PackageCard
        v-for="pkg in packages"
        :key="pkg.slug"
        :name="pkg.name"
        :slug="pkg.slug"
        :role="pkg.role"
        :desc="pkg.desc"
        :version="pkg.version"
        :href="`/${pkg.path}`"
      />
    </div>

    <UPageCard
      variant="naked"
      :ui="{
        container: 'py-5.5 sm:py-5.5',
        wrapper: 'items-stretch gap-2.75',
        header: 'mb-0',
        description: 'font-sans text-sm text-neutral-400',
        footer: 'mt-0 pt-0',
      }"
    >
      <template #header>
        <span class="font-sans text-xl font-semibold tracking-tight text-neutral-50">Let's connect</span>
      </template>

      <template #description>I'm on LinkedIn — feel free to connect if you want to talk Laravel, Linux, or anything in between.</template>

      <template #footer>
        <UButton
          label="Connect on LinkedIn"
          to="https://www.linkedin.com/in/francoismenning/"
          trailing-icon="i-lucide-user-round-plus"
          size="sm"
          external
        />
      </template>
    </UPageCard>
  </UContainer>
</template>
