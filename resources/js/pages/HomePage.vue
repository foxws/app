<script setup lang="ts">
import PackageCard from '@/components/Ui/PackageCard.vue'
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'

interface Package {
  key?: string
  name: string
  slug: string
  path: string
  role: string | null
  desc: string
  version?: string
  flagship: boolean
}

const props = defineProps<{
  packages: Package[]
}>()

const others = computed(() => props.packages.filter((p) => !p.flagship))
</script>

<template>
  <Head title="Home" />

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
    id="packages"
    as="section"
    class="grid grid-cols-1 gap-3.5 px-4 py-6 sm:grid-cols-2 sm:px-7"
  >
    <PackageCard
      v-for="pkg in others"
      :key="pkg.slug"
      :name="pkg.name"
      :slug="pkg.slug"
      :role="pkg.role"
      :desc="pkg.desc"
      :version="pkg.version"
      :href="`/${pkg.path}`"
    />
  </UContainer>
</template>
