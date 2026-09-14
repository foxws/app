<script setup lang="ts">
defineProps<{
  nav: { title: string; path?: string; children?: { title: string; path: string }[] }[]
}>()
</script>

<template>
  <UContentNavigation
    :navigation="nav"
    highlight
  />
</template>
