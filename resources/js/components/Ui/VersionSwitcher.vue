<script setup lang="ts">
import ProjectController from '@/actions/Modules/Marketing/Http/Controllers/ProjectController'
import type { DocsVersion } from '@/types'
import { router } from '@inertiajs/vue3'
import { computed } from 'vue'

const props = defineProps<{
  project: string
  versions: DocsVersion[]
  current?: string
}>()

const items = computed(() => props.versions.map((v: DocsVersion) => v.name))
const current = computed(
  () => props.current ?? props.versions.find((v: DocsVersion) => v.is_default)?.name ?? props.versions[0]?.name,
)

function onSelect(name: string | number | undefined) {
  if (typeof name !== 'string' || name === current.value) {
    return
  }

  const target = props.versions.find((v: DocsVersion) => v.name === name)

  router.visit(
    ProjectController.url({ project: props.project }, { query: target?.is_default ? {} : { version: name } }),
  )
}
</script>

<template>
  <UFormField
    v-if="versions.length"
    label="Version"
    :ui="{
      root: 'flex flex-col gap-2',
      label: 'font-mono text-xs tracking-[.14em] text-neutral-500 uppercase',
    }"
  >
    <USelect
      :model-value="current"
      :items="items"
      :disabled="versions.length <= 1"
      class="w-full"
      :ui="{
        base: 'rounded-lg border-neutral-800 bg-neutral-900 py-2.5 font-sans text-sm font-medium text-neutral-50',
        content: 'rounded-lg border border-neutral-800 bg-neutral-900',
        item: 'font-sans text-sm text-neutral-200',
      }"
      @update:model-value="onSelect"
    />
  </UFormField>
</template>
