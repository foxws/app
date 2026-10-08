<script setup lang="ts">
import type { DocsSurroundLink } from '@/types'
import type { ContentSurroundLink } from '@nuxt/ui/components/content/ContentSurround.vue'
import UContentSurround from '@nuxt/ui/components/content/ContentSurround.vue'

const props = defineProps<{
  surround: (DocsSurroundLink | null)[]
}>()

/** The slot types each link as Nuxt Content's own shape; these are ours. */
const asDocsLink = (link: ContentSurroundLink): DocsSurroundLink => link as unknown as DocsSurroundLink

const isPrevious = (link: ContentSurroundLink): boolean => asDocsLink(link).path === props.surround[0]?.path
</script>

<template>
  <!--
    UContentSurround's own template guards each slot with `v-if="link"` and
    renders a blank placeholder for a missing prev/next — its .d.ts just doesn't
    declare that, so the null entries here need a cast.
  -->
  <UContentSurround
    :surround="surround as unknown as ContentSurroundLink[]"
    :ui="{
      root: 'mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2',
      link: 'group block rounded-3xl border-0 bg-neutral-900 px-6 py-4 ring-1 ring-neutral-800 transition-shadow ring-inset hover:bg-neutral-900 hover:ring-neutral-700 focus-visible:ring-primary',
    }"
  >
    <!-- The slot fills UContentSurround's own link, so this holds only what goes inside it. -->
    <template #link="{ link }">
      <span
        :class="[
          'flex items-center gap-4',
          isPrevious(link) ? 'flex-row-reverse justify-end text-left' : 'justify-end text-right',
        ]"
      >
        <span class="flex min-w-0 flex-col gap-0.5">
          <span class="font-mono text-xs tracking-[.14em] text-neutral-400 uppercase">
            {{ isPrevious(link) ? 'Previous' : 'Next' }}
          </span>
          <span class="truncate font-sans text-base font-semibold text-neutral-50">{{ asDocsLink(link).title }}</span>
        </span>

        <UIcon
          :name="isPrevious(link) ? 'i-lucide-arrow-left' : 'i-lucide-arrow-right'"
          class="size-4 shrink-0 text-neutral-600 transition-colors group-hover:text-neutral-300"
        />
      </span>
    </template>
  </UContentSurround>
</template>
