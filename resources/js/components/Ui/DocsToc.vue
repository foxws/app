<script setup lang="ts">
import UContentToc from '@nuxt/ui/components/content/ContentToc.vue'
import { useTemplateRef } from 'vue'

defineProps<{
  links: { id: string; text: string; children: { id: string; text: string }[] }[]
  ui?: Record<string, string>
}>()

/**
 * Fires whenever a heading link is clicked, before the scroll happens —
 * lets a parent hosting this inside a USlideover/sheet close it first (the
 * sheet's own scroll-lock otherwise swallows the scrollIntoView below).
 */
const emit = defineEmits<{ navigate: [id: string] }>()

const root = useTemplateRef('root')

/**
 * UContentToc's own click handler calls Nuxt's useRouter().push() to jump to
 * the heading — but @nuxt/ui's `router: 'inertia'` stub leaves useRouter()
 * returning undefined, so it throws instead of scrolling anywhere. Headings
 * already have real ids and the anchor already has the right href; we just
 * need to act on it ourselves before the broken handler gets the click.
 */
function onClickCapture(event: MouseEvent) {
  const anchor = (event.target as HTMLElement).closest('a[data-slot="link"]')
  if (!anchor || !root.value?.contains(anchor)) {
    return
  }

  const id = anchor.getAttribute('href')?.replace(/^#/, '')
  if (!id) {
    return
  }

  event.preventDefault()
  event.stopPropagation()

  emit('navigate', id)

  // A parent hosting this inside a USlideover closes it in response to
  // `navigate` (above), but the sheet keeps its scroll lock — and restores
  // the exact scroll position it captured on open — until its 200ms close
  // transition finishes. A `nextTick()` fires well before that and gets
  // clobbered by the restore; wait it out instead.
  setTimeout(() => {
    document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' })
    history.replaceState(null, '', `#${id}`)
  }, 300)
}
</script>

<template>
  <div
    ref="root"
    @click.capture="onClickCapture"
  >
    <!--
      default-open: the mobile variant is a collapsible that starts closed —
      redundant here, since this only ever appears inside our own right rail
      or "On this page" bottom sheet, both of which already give it context.
      No-op on the desktop variant, which isn't collapsible at all.
    -->
    <UContentToc
      :links="links"
      :default-open="true"
      :ui="ui"
    />
  </div>
</template>
