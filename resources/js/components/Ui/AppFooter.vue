<script setup lang="ts">
const year = new Date().getFullYear()
</script>

<template>
  <UFooter
    :ui="{
      root: 'border-t border-neutral-900',
      container:
        'flex flex-wrap items-center justify-between gap-x-6 gap-y-3 px-4 py-6 font-mono text-xs tracking-wider text-neutral-500 uppercase sm:px-7 lg:flex',
      left: 'dot-separated flex items-center gap-0',
      right: 'dot-separated flex items-center gap-0',
    }"
  >
    <template #left>
      <ULink
        to="/terms"
        class="text-neutral-500 hover:text-neutral-300"
        active-class="text-neutral-300"
      >
        Terms
      </ULink>
      <span>© {{ year }}</span>
    </template>

    <template #right>
      <ULink
        to="https://github.com/foxws/app"
        target="_blank"
        class="text-neutral-500 hover:text-neutral-300"
      >
        foxws/app ↗
      </ULink>
      <span>foxws.nl</span>
    </template>
  </UFooter>
</template>
