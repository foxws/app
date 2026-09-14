<script setup lang="ts">
const year = new Date().getFullYear()
</script>

<template>
  <UFooter
    :ui="{
      root: 'border-t border-neutral-900',
      container: 'mx-0 max-w-none flex items-center justify-between px-4 py-5 font-mono text-[11px] uppercase tracking-wider text-neutral-500 sm:px-7',
      left: 'order-1',
      right: 'order-2',
    }"
  >
    <template #left>
      <span>foxws.nl</span>
    </template>
    <template #right>
      <span>MIT licence · {{ year }}</span>
    </template>
  </UFooter>
</template>
