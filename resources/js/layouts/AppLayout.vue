<script setup lang="ts">
import AppFooter from '@/components/Ui/AppFooter.vue'
import AppHeader from '@/components/Ui/AppHeader.vue'
import { useAppearance } from '@/composables/appearance'
import { useFlash } from '@/composables/flash'

const { nonce } = useAppearance()
useFlash()
</script>

<template>
  <UApp :nonce="nonce">
    <div class="min-h-dvh bg-neutral-950 font-sans text-neutral-50">
      <AppHeader />

      <slot />

      <AppFooter />
    </div>
  </UApp>
</template>
