<script setup lang="ts">
defineProps<{
  html: string
}>()

const proseClass =
  'flex flex-col gap-4 overflow-x-hidden font-sans text-base leading-relaxed wrap-break-word text-neutral-400 ' +
  '[&_h2]:mt-8 [&_h2]:text-xl [&_h2]:font-semibold [&_h2]:tracking-tight [&_h2]:text-neutral-50 ' +
  '[&_h3]:mt-6 [&_h3]:text-base [&_h3]:font-semibold [&_h3]:text-neutral-50 ' +
  '[&_a]:text-identity-500 [&_a]:underline [&_a]:underline-offset-2 ' +
  '[&_code]:rounded [&_code]:bg-neutral-900 [&_code]:px-1.5 [&_code]:py-0.5 [&_code]:font-mono [&_code]:text-sm [&_code]:text-neutral-200 ' +
  '[&_pre]:overflow-x-auto [&_pre]:rounded-lg [&_pre]:border [&_pre]:border-neutral-900 [&_pre]:bg-neutral-900 [&_pre]:p-3.5 [&_pre]:font-mono [&_pre]:text-sm [&_pre]:leading-[1.85] ' +
  '[&_pre_code]:bg-transparent [&_pre_code]:p-0 ' +
  '[&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5 [&_li]:my-1 ' +
  '[&_blockquote]:border-l [&_blockquote]:border-neutral-800 [&_blockquote]:pl-4 [&_blockquote]:text-neutral-500 ' +
  '[&_table]:block [&_table]:w-full [&_table]:max-w-full [&_table]:overflow-x-auto [&_table]:border [&_table]:border-collapse [&_table]:border-neutral-800 ' +
  '[&_th]:border [&_th]:border-neutral-800 [&_th]:bg-neutral-900 [&_th]:px-3 [&_th]:py-2 [&_th]:text-left [&_th]:font-semibold [&_th]:text-neutral-50 ' +
  '[&_td]:border [&_td]:border-neutral-800 [&_td]:px-3 [&_td]:py-2 ' +
  '[&_.callout]:flex [&_.callout]:flex-col [&_.callout]:gap-3 [&_.callout]:rounded-lg [&_.callout]:border [&_.callout]:border-l-2 [&_.callout]:border-neutral-800 [&_.callout]:bg-neutral-900/50 [&_.callout]:px-4 [&_.callout]:py-3 ' +
  '[&_.callout]:before:text-xs [&_.callout]:before:font-semibold [&_.callout]:before:tracking-wide [&_.callout]:before:uppercase [&_.callout:has(>.callout-title)]:before:hidden ' +
  '[&_.callout-title]:font-semibold [&_.callout-title]:text-neutral-50 ' +
  "[&_.callout-note]:border-l-sky-500 [&_.callout-note]:before:text-sky-400 [&_.callout-note]:before:content-['Note'] " +
  "[&_.callout-info]:border-l-sky-500 [&_.callout-info]:before:text-sky-400 [&_.callout-info]:before:content-['Info'] " +
  "[&_.callout-tip]:border-l-emerald-500 [&_.callout-tip]:before:text-emerald-400 [&_.callout-tip]:before:content-['Tip'] " +
  "[&_.callout-warning]:border-l-amber-500 [&_.callout-warning]:before:text-amber-400 [&_.callout-warning]:before:content-['Warning'] " +
  "[&_.callout-caution]:border-l-orange-500 [&_.callout-caution]:before:text-orange-400 [&_.callout-caution]:before:content-['Caution'] " +
  "[&_.callout-danger]:border-l-red-500 [&_.callout-danger]:before:text-red-400 [&_.callout-danger]:before:content-['Danger'] " +
  "[&_.callout-error]:border-l-red-500 [&_.callout-error]:before:text-red-400 [&_.callout-error]:before:content-['Error'] " +
  "[&_.callout-important]:border-l-violet-500 [&_.callout-important]:before:text-violet-400 [&_.callout-important]:before:content-['Important'] " +
  "[&_.callout-success]:border-l-emerald-500 [&_.callout-success]:before:text-emerald-400 [&_.callout-success]:before:content-['Success']"
</script>

<template>
  <div
    :class="proseClass"
    v-html="html"
  />
</template>
