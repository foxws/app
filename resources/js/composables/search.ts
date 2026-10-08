import { createSharedComposable } from '@vueuse/core'
import { ref } from 'vue'

/**
 * One open state for the site search palette that AppHeader renders.
 */
export const useSearchPalette = createSharedComposable(() => {
  const open = ref(false)

  return {
    open,
  }
})
