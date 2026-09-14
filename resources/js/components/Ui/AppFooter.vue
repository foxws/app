<script setup lang="ts">
const year = new Date().getFullYear()
</script>

<template>
  <footer class="flex justify-between border-t border-neutral-900 px-4 py-5 font-mono text-[11px] tracking-wider text-neutral-500 sm:px-7">
    <span>FOXWS.NL</span>
    <span>MIT LICENCE · {{ year }}</span>
  </footer>
</template>
