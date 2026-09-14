<script setup lang="ts">
import AppLogo from '@/components/Ui/AppLogo.vue'
import CommandPalette from '@/components/Ui/CommandPalette.vue'
import SearchTrigger from '@/components/Ui/SearchTrigger.vue'
import { useAppearance } from '@/composables/appearance'
import { usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const { app } = useAppearance()

interface Crumb {
  label: string
  href?: string
}

const crumbs = computed<Crumb[]>(() => (usePage().props.crumbs as Crumb[] | undefined) ?? [])

const breadcrumbItems = computed(() => {
  const items = [{ label: app.value, to: '/' }, ...crumbs.value.map((c) => ({ label: c.label, to: c.href }))]

  // The current page's own crumb (the last one, when there is one beyond the
  // app root) reads brighter than the links leading up to it.
  return items.map((item, index) => ({
    ...item,
    ui: index > 0 && index === items.length - 1 ? { link: 'text-neutral-50' } : undefined,
  }))
})

const scope = computed<string | undefined>(() => usePage().props.scope as string | undefined)

const searchOpen = ref(false)
</script>

<template>
  <UHeader
    :toggle="false"
    :ui="{
      root: 'sticky top-0 z-50 border-neutral-900 bg-neutral-950/95',
      container: 'px-4 sm:px-7',
    }"
  >
    <template #left>
      <AppLogo
        :label="false"
        :size="26"
      />

      <UBreadcrumb
        :items="breadcrumbItems"
        class="ml-2.5 min-w-0"
        :ui="{
          list: 'flex-nowrap',
          link: 'min-w-0 font-mono text-[13px] font-normal text-neutral-500 transition-colors hover:text-neutral-300',
          linkLabel: 'min-w-0 truncate',
        }"
      >
        <template #separator>
          <span class="font-mono text-[13px] text-neutral-500">/</span>
        </template>
      </UBreadcrumb>
    </template>

    <template #right>
      <SearchTrigger
        v-model:open="searchOpen"
        :scope="scope"
      />

      <UButton
        to="https://github.com/foxws"
        variant="ghost"
        color="neutral"
        icon="i-lucide-github"
        external
      />
    </template>
  </UHeader>

  <CommandPalette
    v-model:open="searchOpen"
    :scope="scope"
  />
</template>
