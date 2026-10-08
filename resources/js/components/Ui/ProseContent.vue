<script setup lang="ts">
import { useClipboard } from '@vueuse/core'
import { nextTick, onMounted, useTemplateRef, watch } from 'vue'

const props = withDefaults(
  defineProps<{
    html: string
    lead?: boolean
  }>(),
  {
    lead: false,
  },
)

const proseClass =
  'flex flex-col font-sans leading-relaxed wrap-break-word ' +
  '[&_h2]:mt-7 [&_h2]:scroll-mt-22 [&_h2]:text-2xl [&_h2]:font-semibold [&_h2]:tracking-tight [&_h2]:text-neutral-50 [&>h2:first-child]:mt-0 ' +
  '[&_h3]:mt-6 [&_h3]:scroll-mt-22 [&_h3]:text-lg [&_h3]:font-semibold [&_h3]:text-neutral-50 ' +
  '[&_p]:text-pretty ' +
  '[&_a]:text-identity-500 [&_a]:underline [&_a]:decoration-1 [&_a]:underline-offset-3 [&_a:hover]:text-identity-400 [&_a:hover]:decoration-2 ' +
  '[&_code]:rounded [&_code]:bg-neutral-900 [&_code]:px-1.5 [&_code]:py-0.5 [&_code]:font-mono [&_code]:text-sm [&_code]:text-neutral-200 ' +
  '[&_pre]:overflow-x-auto [&_pre]:rounded-lg [&_pre]:bg-neutral-900 [&_pre]:px-4.5 [&_pre]:py-3.5 [&_pre]:font-mono [&_pre]:text-sm [&_pre]:leading-[1.85] [&_pre]:text-neutral-200 [&_pre]:shadow-[inset_0_1px_0_rgb(255_255_255/0.05)] ' +
  '[&_pre_code]:bg-transparent [&_pre_code]:p-0 ' +
  '[&_.code-block]:relative ' +
  '[&_.code-block>button]:absolute [&_.code-block>button]:top-3 [&_.code-block>button]:right-3 [&_.code-block>button]:h-7 [&_.code-block>button]:rounded-full [&_.code-block>button]:bg-neutral-800 [&_.code-block>button]:px-2.5 [&_.code-block>button]:font-mono [&_.code-block>button]:text-xs [&_.code-block>button]:tracking-wider [&_.code-block>button]:text-neutral-200 [&_.code-block>button]:uppercase [&_.code-block>button]:opacity-0 [&_.code-block>button]:ring-1 [&_.code-block>button]:ring-neutral-700 [&_.code-block>button]:transition-opacity [&_.code-block>button]:ring-inset ' +
  '[&_.code-block:hover>button]:opacity-100 [&_.code-block>button:focus-visible]:opacity-100 [&_.code-block>button:hover]:bg-neutral-700 [&_.code-block>button:hover]:text-neutral-50 [&_.code-block>button[data-copied]]:text-gold-300 [&_.code-block>button[data-copied]]:opacity-100 pointer-coarse:[&_.code-block>button]:opacity-100 ' +
  '[&_ul]:flex [&_ul]:list-disc [&_ul]:flex-col [&_ul]:gap-2 [&_ul]:pl-5 [&_ol]:flex [&_ol]:list-decimal [&_ol]:flex-col [&_ol]:gap-2 [&_ol]:pl-5 ' +
  '[&_blockquote]:border-l [&_blockquote]:border-neutral-800 [&_blockquote]:pl-4 [&_blockquote]:text-neutral-500 ' +
  '[&_.table-block]:overflow-x-auto ' +
  '[&_table]:w-full [&_table]:border-collapse [&_table]:text-sm ' +
  '[&_th]:pr-6 [&_th]:pb-2.5 [&_th]:text-left [&_th]:font-mono [&_th]:text-xs [&_th]:font-normal [&_th]:tracking-[.14em] [&_th]:text-neutral-500 [&_th]:uppercase [&_th:last-child]:pr-0 ' +
  '[&_td]:border-t [&_td]:border-neutral-800 [&_td]:py-3 [&_td]:pr-6 [&_td]:align-top [&_td:first-child]:whitespace-nowrap [&_td:last-child]:pr-0 ' +
  '[&_.callout]:flex [&_.callout]:flex-col [&_.callout]:gap-1 [&_.callout]:border-y [&_.callout]:border-neutral-800 [&_.callout]:bg-linear-to-r [&_.callout]:to-transparent [&_.callout]:to-80% [&_.callout]:px-5 [&_.callout]:py-4 [&_.callout]:text-neutral-300 ' +
  '[&_.callout]:before:font-mono [&_.callout]:before:text-xs [&_.callout]:before:tracking-[.14em] [&_.callout]:before:uppercase [&_.callout:has(>.callout-title)]:before:hidden ' +
  '[&_.callout-title]:font-semibold [&_.callout-title]:text-neutral-50 ' +
  "[&_.callout-note]:border-sky-500/40 [&_.callout-note]:from-sky-500/14 [&_.callout-note]:before:text-sky-400 [&_.callout-note]:before:content-['Note'] " +
  "[&_.callout-info]:border-sky-500/40 [&_.callout-info]:from-sky-500/14 [&_.callout-info]:before:text-sky-400 [&_.callout-info]:before:content-['Info'] " +
  "[&_.callout-tip]:border-emerald-500/40 [&_.callout-tip]:from-emerald-500/14 [&_.callout-tip]:before:text-emerald-400 [&_.callout-tip]:before:content-['Tip'] " +
  "[&_.callout-warning]:border-amber-500/40 [&_.callout-warning]:from-amber-500/14 [&_.callout-warning]:before:text-amber-400 [&_.callout-warning]:before:content-['Warning'] " +
  "[&_.callout-caution]:border-orange-500/40 [&_.callout-caution]:from-orange-500/14 [&_.callout-caution]:before:text-orange-400 [&_.callout-caution]:before:content-['Caution'] " +
  "[&_.callout-danger]:border-red-500/40 [&_.callout-danger]:from-red-500/14 [&_.callout-danger]:before:text-red-400 [&_.callout-danger]:before:content-['Danger'] " +
  "[&_.callout-error]:border-red-500/40 [&_.callout-error]:from-red-500/14 [&_.callout-error]:before:text-red-400 [&_.callout-error]:before:content-['Error'] " +
  "[&_.callout-important]:border-violet-500/40 [&_.callout-important]:from-violet-500/14 [&_.callout-important]:before:text-violet-400 [&_.callout-important]:before:content-['Important'] " +
  "[&_.callout-success]:border-emerald-500/40 [&_.callout-success]:from-emerald-500/14 [&_.callout-success]:before:text-emerald-400 [&_.callout-success]:before:content-['Success']"

const root = useTemplateRef<HTMLElement>('root')

const { copy } = useClipboard({ legacy: true })

/**
 * The rendered markdown has no room for UI of its own, so code blocks get
 * their copy button, and tables their scroll box, once it is in the DOM.
 */
function enhance() {
  root.value?.querySelectorAll('pre').forEach((pre) => {
    if (pre.parentElement?.classList.contains('code-block')) {
      return
    }

    const block = document.createElement('div')
    block.className = 'code-block'

    const button = document.createElement('button')
    button.type = 'button'
    button.dataset.copy = ''
    button.textContent = 'Copy'

    pre.before(block)
    block.append(pre, button)
  })

  root.value?.querySelectorAll('table').forEach((table) => {
    if (table.parentElement?.classList.contains('table-block')) {
      return
    }

    const box = document.createElement('div')
    box.className = 'table-block'

    table.before(box)
    box.append(table)
  })
}

async function onClick(event: MouseEvent) {
  const button = (event.target as HTMLElement).closest<HTMLButtonElement>('button[data-copy]')
  const code = button?.parentElement?.querySelector('pre')

  if (!button || !code) {
    return
  }

  await copy(code.textContent ?? '')

  button.textContent = 'Copied'
  button.dataset.copied = ''

  setTimeout(() => {
    button.textContent = 'Copy'
    delete button.dataset.copied
  }, 1600)
}

onMounted(enhance)

watch(
  () => props.html,
  async () => {
    await nextTick()
    enhance()
  },
)
</script>

<template>
  <div
    ref="root"
    :class="[proseClass, lead ? 'gap-7 text-base text-neutral-300 sm:text-lg' : 'gap-4 text-base text-neutral-400']"
    @click="onClick"
    v-html="html"
  />
</template>
