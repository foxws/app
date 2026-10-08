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
  <USelect
    v-if="versions.length"
    :model-value="current"
    :items="items"
    :disabled="versions.length <= 1"
    aria-label="Version"
    class="w-full"
    :ui="{
      base: 'h-10 rounded-full bg-neutral-900 ps-4 pe-9 font-mono text-sm text-neutral-50 ring-neutral-800 disabled:opacity-100',
      trailingIcon: 'size-4 text-neutral-400',
      content: 'rounded-2xl bg-neutral-900 ring-neutral-800',
      item: 'rounded-lg font-mono text-sm text-neutral-200',
    }"
    @update:model-value="onSelect"
  />
</template>
