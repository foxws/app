import { createSharedComposable } from '@vueuse/core'
import { ref } from 'vue'

/**
 * One open state for the site search, so the header and the docs sidebar
 * can both open the palette that AppHeader renders.
 */
export const useSearchPalette = createSharedComposable(() => {
  const open = ref(false)

  return {
    open,
  }
})
