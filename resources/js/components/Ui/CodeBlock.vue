<script setup lang="ts">
defineProps<{
  filename: string
  language: string
  code: string
}>()
</script>

<template>
  <div class="overflow-hidden rounded-lg border border-neutral-900 bg-neutral-900">
    <div class="flex items-center justify-between border-b border-neutral-900 px-3.5 py-2.25 font-mono text-[11px] text-neutral-500">
      <span>{{ filename }}</span>
      <span class="rounded-full bg-neutral-800 px-1.75 py-0.5 text-neutral-300">{{ language }}</span>
    </div>
    <pre class="overflow-x-auto p-3.5 font-mono text-[13px] leading-[1.85] text-neutral-200"><code>{{ code }}</code></pre>
  </div>
</template>
