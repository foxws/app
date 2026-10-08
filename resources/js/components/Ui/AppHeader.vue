<script setup lang="ts">
import AppLogo from '@/components/Ui/AppLogo.vue'
import CommandPalette from '@/components/Ui/CommandPalette.vue'
import SearchTrigger from '@/components/Ui/SearchTrigger.vue'
import { useAppearance } from '@/composables/appearance'
import { useSearchPalette } from '@/composables/search'
import { usePage } from '@inertiajs/vue3'
import { defineShortcuts } from '@nuxt/ui/composables'
import { computed } from 'vue'

const { app } = useAppearance()

interface Crumb {
  label: string
  href?: string
}

const crumbs = computed<Crumb[]>(() => (usePage().props.crumbs as Crumb[] | undefined) ?? [])

const breadcrumbItems = computed(() => {
  const items = [{ label: app.value, to: '/' }, ...crumbs.value.map((c) => ({ label: c.label, to: c.href }))]

  // The current page's own crumb (the last one) reads brighter than the links leading up to it.
  return items.map((item, index) => ({
    ...item,
    ui: index === items.length - 1 ? { link: 'text-neutral-50' } : undefined,
  }))
})

const scope = computed<string | undefined>(() => usePage().props.scope as string | undefined)

const { open: searchOpen } = useSearchPalette()

defineShortcuts({ meta_k: () => (searchOpen.value = true) })
</script>

<template>
  <UHeader
    :toggle="false"
    :ui="{
      root: 'sticky top-0 z-50 border-neutral-900 bg-neutral-950/95',
      container: 'px-4 sm:px-7',
      left: 'min-w-0',
      right: 'gap-2',
    }"
  >
    <template #left>
      <AppLogo :label="false" />

      <UBreadcrumb
        :items="breadcrumbItems"
        class="ml-2.5 min-w-0"
        :ui="{
          list: 'flex-nowrap gap-2.5',
          link: 'min-w-0 font-mono text-sm font-normal text-neutral-400 transition-colors hover:text-neutral-50',
          linkLabel: 'min-w-0 truncate lowercase',
        }"
      >
        <template #separator>
          <span class="font-mono text-sm text-neutral-600">/</span>
        </template>
      </UBreadcrumb>
    </template>

    <template #right>
      <SearchTrigger />

      <UButton
        to="https://github.com/foxws"
        aria-label="Foxws on GitHub"
        variant="link"
        color="neutral"
        icon="i-lucide-github"
        class="size-11 justify-center text-neutral-500 hover:text-neutral-300"
      />
    </template>
  </UHeader>

  <CommandPalette
    v-model:open="searchOpen"
    :scope="scope"
  />
</template>
