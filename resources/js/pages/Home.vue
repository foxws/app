<script setup lang="ts">
import PackageCard from '@/components/Ui/PackageCard.vue'
import StryCallout from '@/components/Ui/StryCallout.vue'
import { Head } from '@inertiajs/vue3'
import { computed } from 'vue'

interface Package {
  key?: string
  name: string
  slug: string
  role: string | null
  desc: string
  version?: string
  flagship: boolean
}

const props = defineProps<{
  packages: Package[]
}>()

const stry = computed(() => props.packages.find((p) => p.flagship))
const others = computed(() => props.packages.filter((p) => !p.flagship))

const keyFor = (pkg: Package) => pkg.slug.replace('foxws/', '')
</script>

<template>
  <Head title="Home" />

  <section class="flex flex-col gap-6 border-b border-neutral-900 px-4 py-16 sm:px-7 sm:py-19">
    <span class="font-mono text-[10px] tracking-[.18em] text-identity-500">LARAVEL INFRASTRUCTURE PACKAGES</span>
    <h1 class="max-w-4xl font-sans text-4xl leading-[.98] font-semibold tracking-tight text-neutral-50 text-pretty sm:text-7xl">
      The hard parts, already solved.
    </h1>
    <p class="max-w-xl font-sans text-base leading-relaxed text-neutral-400 sm:text-lg">
      Every site eventually needs containers it can trust, video that plays everywhere, and a cache that
      invalidates itself. We package those layers so you can go back to building the actual product.
    </p>
    <div class="mt-1.5 flex flex-col gap-2.25 sm:flex-row">
      <UButton
        to="#packages"
        class="justify-center rounded-lg bg-identity-500 px-5 py-3 font-sans text-[13px] font-semibold text-neutral-950 hover:bg-identity-400"
      >
        Browse packages
      </UButton>
      <UButton
        to="/docs"
        variant="outline"
        color="neutral"
        class="justify-center rounded-lg px-5 py-3 font-sans text-[13px] font-medium"
      >
        Read the docs
      </UButton>
    </div>
  </section>

  <section
    id="packages"
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
      :href="`/${keyFor(pkg)}`"
    />
  </section>

  <section
    v-if="stry"
    class="px-4 pb-7 sm:px-7"
  >
    <StryCallout :href="`/${keyFor(stry)}`" />
  </section>
</template>
