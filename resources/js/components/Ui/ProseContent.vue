<script setup lang="ts">
defineProps<{
  html: string
}>()

const proseClass =
  'flex flex-col gap-4 font-sans text-[15px] leading-relaxed text-neutral-400 ' +
  '[&_h2]:mt-8 [&_h2]:text-xl [&_h2]:font-semibold [&_h2]:tracking-tight [&_h2]:text-neutral-50 ' +
  '[&_h3]:mt-6 [&_h3]:text-base [&_h3]:font-semibold [&_h3]:text-neutral-50 ' +
  '[&_a]:text-identity-500 [&_a]:underline [&_a]:underline-offset-2 ' +
  '[&_code]:rounded [&_code]:bg-neutral-900 [&_code]:px-1.5 [&_code]:py-0.5 [&_code]:font-mono [&_code]:text-[13px] [&_code]:text-neutral-200 ' +
  '[&_pre]:overflow-x-auto [&_pre]:rounded-lg [&_pre]:border [&_pre]:border-neutral-900 [&_pre]:bg-neutral-900 [&_pre]:p-3.5 [&_pre]:font-mono [&_pre]:text-[13px] [&_pre]:leading-[1.85] ' +
  '[&_pre_code]:bg-transparent [&_pre_code]:p-0 ' +
  '[&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5 [&_li]:my-1 ' +
  '[&_blockquote]:border-l [&_blockquote]:border-neutral-800 [&_blockquote]:pl-4 [&_blockquote]:text-neutral-500'
</script>

<template>
  <div
    :class="proseClass"
    v-html="html"
  />
</template>
