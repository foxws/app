import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

export function useAppearance() {
  const app = computed(() => usePage().props.app)
  const nonce = computed(() => usePage().props.nonce)

  return {
    app,
    nonce,
  }
}
