<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  versions: { name: string; is_default: boolean }[]
}>()

const items = computed(() => props.versions.map((v) => v.name))
const current = computed(() => props.versions.find((v) => v.is_default)?.name ?? props.versions[0]?.name)
</script>

<template>
  <div
    v-if="versions.length"
    class="flex flex-col gap-2"
  >
    <span class="font-mono text-[10px] tracking-[.14em] text-neutral-500 uppercase">Version</span>
    <!--
      Only one version is ever synced/routable today (DocumentController and
      ProjectController always resolve the project's default version) — this
      switcher is UI-only until multi-version routing lands, so it stays
      disabled until there's more than one version to actually switch to.
    -->
    <USelect
      :model-value="current"
      :items="items"
      :disabled="versions.length <= 1"
      class="w-full"
      :ui="{
        base: 'rounded-lg border-neutral-800 bg-neutral-900 py-2.5 font-sans text-[13px] font-medium text-neutral-50',
        content: 'rounded-lg border border-neutral-800 bg-neutral-900',
        item: 'font-sans text-[13px] text-neutral-200',
      }"
    />
  </div>
</template>
