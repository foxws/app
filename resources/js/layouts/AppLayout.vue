<script setup lang="ts">
import AppFooter from '@/components/Ui/AppFooter.vue'
import AppHeader from '@/components/Ui/AppHeader.vue'
import { useAppearance } from '@/composables/appearance'
import { useFlash } from '@/composables/flash'
import { Head } from '@inertiajs/vue3'

const { nonce } = useAppearance()
useFlash()
</script>

<template>
  <Head>
    <meta
      head-key="description"
      name="description"
      content="Laravel packages, Linux tooling and AI-assisted development for video streaming, documentation, domain-driven design and Podman deployments."
    />
    <meta
      head-key="robots"
      name="robots"
      content="index, follow"
    />
  </Head>

  <UApp :nonce="nonce">
    <div class="min-h-dvh overflow-x-clip bg-neutral-950 font-sans text-neutral-50">
      <AppHeader />

      <main>
        <slot />
      </main>

      <AppFooter />
    </div>
  </UApp>
</template>
