<script setup lang="ts">
const year = new Date().getFullYear()
</script>

<template>
  <UFooter
    :ui="{
      root: 'border-t border-neutral-900',
      container:
        'flex flex-col items-center justify-center gap-3 px-4 py-5 font-mono text-xs tracking-wider text-neutral-500 uppercase sm:flex-row sm:justify-between sm:px-7',
      left: 'order-1 mt-0',
      center: 'order-2 mt-0',
      right: 'order-3',
    }"
  >
    <template #left>
      <span>foxws.nl</span>
    </template>

    <UButton
      to="https://github.com/foxws"
      variant="ghost"
      color="neutral"
      size="xs"
      icon="i-lucide-github"
    />

    <template #right>
      <ULink
        to="/terms"
        active-class="text-inherit"
      >
        Terms
      </ULink>
      <span aria-hidden="true">·</span>
      <span>© {{ year }}</span>
    </template>
  </UFooter>
</template>
