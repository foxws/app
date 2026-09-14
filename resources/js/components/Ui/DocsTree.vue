<script setup lang="ts">
defineProps<{
  nav: { group: string; items: string[] }[]
}>()
</script>

<template>
  <nav class="flex flex-col gap-4.5">
    <div
      v-for="g in nav"
      :key="g.group"
      class="flex flex-col gap-0.5"
    >
      <span class="px-2 pb-1.5 font-mono text-[10px] tracking-[.14em] text-neutral-500 uppercase">{{ g.group }}</span>
      <span
        v-for="i in g.items"
        :key="i"
        class="cursor-default rounded-md px-2.25 py-1.5 font-sans text-[13px] font-medium text-neutral-400 hover:bg-neutral-900 hover:text-neutral-50"
      >{{ i }}</span>
    </div>
  </nav>
</template>
