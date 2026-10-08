<script setup lang="ts">
import FeaturedProject from '@/components/Ui/FeaturedProject.vue'
import PackageRow from '@/components/Ui/PackageRow.vue'
import type { DocsPackageGroup, DocsSideProjectSummary } from '@/types'
import { Head } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const props = defineProps<{
  packageGroups: DocsPackageGroup[]
  sideProjects: DocsSideProjectSummary[]
}>()

const focusAreas = ['Laravel', 'AI', 'Linux', 'Cloud']

const allGroups = 'all'
const ungroupedLabel = 'More'

const activeGroup = ref<string>(allGroups)
const filter = ref('')

const packages = computed(() =>
  props.packageGroups.flatMap((group) =>
    group.packages.map((pkg) => ({ ...pkg, group: group.name ?? ungroupedLabel })),
  ),
)

const chips = computed(() => [
  { label: 'All', value: allGroups, count: packages.value.length },
  ...props.packageGroups.map((group) => ({
    label: group.name ?? ungroupedLabel,
    value: group.name ?? ungroupedLabel,
    count: group.packages.length,
  })),
])

/** Most-installed first, so the list leads with what people actually use. */
const visiblePackages = computed(() => {
  const needle = filter.value.trim().toLowerCase()

  return packages.value
    .filter((pkg) => activeGroup.value === allGroups || pkg.group === activeGroup.value)
    .filter(
      (pkg) => needle === '' || [pkg.name, pkg.slug, pkg.desc, pkg.group].join(' ').toLowerCase().includes(needle),
    )
    .sort((a, b) => (b.downloads ?? 0) - (a.downloads ?? 0) || a.name.localeCompare(b.name))
})

function resetFilters() {
  activeGroup.value = allGroups
  filter.value = ''
}
</script>

<template>
  <Head title="Home">
    <meta
      head-key="description"
      name="description"
      content="I build Laravel packages and tools for video streaming, documentation, domain-driven design, and Podman-based deployments."
    />
  </Head>

  <UContainer
    as="section"
    class="px-4 pt-7 sm:px-7"
  >
    <div
      class="relative grid items-center gap-x-14 gap-y-8 overflow-hidden rounded-4xl px-6 py-8 bg-hero-panel sm:px-12 sm:py-12 lg:grid-cols-[minmax(0,1.7fr)_minmax(0,1fr)] lg:px-16 lg:py-14"
    >
      <div
        aria-hidden="true"
        class="pointer-events-none absolute inset-y-0 right-0 w-[46%]"
      >
        <div
          class="absolute inset-0 bg-linear-200 from-gold-300/32 to-gold-400/10 [clip-path:polygon(38%_0,100%_0,100%_62%)]"
        />
        <div
          class="absolute inset-0 bg-linear-200 from-gold-500/28 to-plum-700/12 [clip-path:polygon(38%_0,100%_62%,100%_100%,62%_100%)]"
        />
        <div
          class="absolute inset-0 bg-linear-160 from-plum-700/24 to-plum-900/4 [clip-path:polygon(38%_0,62%_100%,0_46%)]"
        />
      </div>

      <div class="relative flex min-w-0 flex-col gap-5">
        <h1
          class="max-w-160 font-sans text-4xl leading-[.98] font-semibold tracking-tight text-neutral-50 sm:text-5xl lg:text-6xl"
        >
          <span class="block">The hard parts,</span>
          <span class="block text-gradient-gold">already solved.</span>
        </h1>

        <p class="max-w-136 font-sans text-base leading-relaxed text-pretty text-neutral-300 sm:text-lg">
          I build Laravel packages and tools for video streaming, documentation, domain-driven design, and Podman-based
          deployments.
        </p>

        <div class="mt-2 flex flex-wrap items-center gap-x-5 gap-y-3">
          <UButton
            to="#packages"
            label="Browse packages"
            trailing-icon="i-lucide-arrow-down"
            color="neutral"
            variant="solid"
            class="h-12 rounded-full bg-neutral-50 px-5.5 font-sans text-sm font-medium text-neutral-950 hover:bg-neutral-200"
          />
        </div>
      </div>

      <div
        class="relative flex min-w-0 flex-col gap-3.5 rounded-3xl bg-neutral-950/42 px-7 pt-6 pb-3 ring-1 ring-neutral-50/12 backdrop-blur-md ring-inset"
      >
        <h2 class="font-mono text-xs tracking-[.18em] text-gold-300 uppercase">What I do</h2>

        <ul class="flex flex-col">
          <li
            v-for="area in focusAreas"
            :key="area"
            class="border-t border-neutral-50/12 py-3 font-sans text-[22px] leading-7 font-semibold tracking-tight text-neutral-50"
          >
            {{ area }}
          </li>
        </ul>
      </div>
    </div>
  </UContainer>

  <section
    v-if="sideProjects.length"
    id="projects"
    class="border-b border-neutral-900"
  >
    <UContainer class="flex flex-col gap-6 px-4 py-12 sm:px-7">
      <h2 class="font-mono text-xs tracking-[.14em] text-neutral-500 uppercase">Featured work</h2>

      <div class="grid grid-cols-1 gap-x-8 gap-y-10 sm:grid-cols-2 lg:grid-cols-4">
        <FeaturedProject
          v-for="project in sideProjects"
          :key="project.slug"
          :name="project.name"
          :desc="project.desc"
          :href="project.href"
        />
      </div>
    </UContainer>
  </section>

  <UContainer
    id="packages"
    as="section"
    class="scroll-mt-(--ui-header-height) px-4 pt-12 pb-12 sm:px-7"
  >
    <div class="flex flex-wrap items-baseline justify-between gap-x-6 gap-y-2">
      <h2 class="font-sans text-3xl font-semibold tracking-tight text-neutral-50">Packages</h2>
      <span
        aria-live="polite"
        class="font-mono text-xs tracking-wider text-neutral-500"
      >
        {{ visiblePackages.length }} of {{ packages.length }}
      </span>
    </div>

    <div class="mt-6 flex flex-wrap items-center justify-between gap-x-6 gap-y-3 pb-8">
      <div
        v-if="chips.length > 2"
        role="group"
        aria-label="Filter by area"
        class="flex flex-wrap gap-2"
      >
        <UButton
          v-for="chip in chips"
          :key="chip.value"
          type="button"
          color="neutral"
          variant="ghost"
          :aria-pressed="activeGroup === chip.value"
          :class="[
            'h-10 gap-2 rounded-full px-3.5 font-mono text-xs tracking-wider uppercase',
            activeGroup === chip.value
              ? 'bg-identity-500 text-neutral-950 hover:bg-identity-400 hover:text-neutral-950'
              : 'text-neutral-400 ring-1 ring-neutral-800 ring-inset hover:bg-transparent hover:text-neutral-300 hover:ring-neutral-700',
          ]"
          @click="activeGroup = chip.value"
        >
          <span>{{ chip.label }}</span>
          <span class="opacity-60">{{ chip.count }}</span>
        </UButton>
      </div>

      <UInput
        v-model="filter"
        type="search"
        icon="i-lucide-search"
        placeholder="Filter packages"
        aria-label="Filter packages"
        autocomplete="off"
        class="ms-auto max-w-80 min-w-55 flex-1"
        :ui="{
          base: 'h-10 rounded-full bg-neutral-900 ps-10 font-mono text-sm text-neutral-50 ring-neutral-800 placeholder:text-neutral-500 focus-visible:ring-neutral-700',
          leadingIcon: 'size-4 text-neutral-500',
        }"
      />
    </div>

    <div
      v-if="visiblePackages.length"
      class="gap-x-12 lg:columns-2"
    >
      <PackageRow
        v-for="pkg in visiblePackages"
        :key="pkg.slug"
        :name="pkg.name"
        :desc="pkg.desc"
        :href="pkg.href"
        :downloads="pkg.downloads"
      />
    </div>

    <div
      v-else
      class="flex flex-col items-start gap-4 pt-8 pb-4"
    >
      <p class="font-sans text-base leading-relaxed text-neutral-400">
        No packages match
        <span
          v-if="filter"
          class="font-mono text-sm text-neutral-50"
          >{{ filter }}</span
        ><template v-else>this filter</template>.
      </p>

      <UButton
        label="Clear filters"
        color="neutral"
        variant="outline"
        class="h-11 rounded-full px-5 font-sans text-sm font-medium text-neutral-50 ring-neutral-50/28 hover:bg-neutral-50/8"
        @click="resetFilters"
      />
    </div>
  </UContainer>

  <UContainer
    as="section"
    class="px-4 pb-12 sm:px-7"
  >
    <div
      class="flex flex-wrap items-center justify-between gap-x-8 gap-y-5 rounded-4xl p-8 ring-1 ring-neutral-800 bg-cta-panel ring-inset sm:p-12"
    >
      <div class="flex min-w-0 flex-[1_1_20rem] flex-col gap-2">
        <h2 class="font-sans text-3xl font-semibold tracking-tight text-neutral-50">Let's connect</h2>
        <p class="max-w-136 font-sans text-sm text-neutral-400">
          Always up for a conversation about Laravel, Linux, or anything in between.
        </p>
      </div>

      <UButton
        to="https://www.linkedin.com/in/francoismenning/"
        target="_blank"
        label="Connect on LinkedIn"
        icon="i-lucide-user-round-plus"
        color="neutral"
        variant="solid"
        class="h-11 rounded-full bg-neutral-50 px-5 font-sans text-sm font-medium text-neutral-950 hover:bg-neutral-200"
      />
    </div>
  </UContainer>
</template>
