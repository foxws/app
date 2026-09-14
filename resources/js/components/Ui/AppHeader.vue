<script setup lang="ts">
import AppLogo from '@/components/Ui/AppLogo.vue'
import CommandPalette from '@/components/Ui/CommandPalette.vue'
import SearchTrigger from '@/components/Ui/SearchTrigger.vue'
import { useAppearance } from '@/composables/appearance'
import { usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'

const { app } = useAppearance()

const crumbs = computed<string[]>(() => (usePage().props.crumbs as string[] | undefined) ?? [])
const scope = computed<string | undefined>(() => usePage().props.scope as string | undefined)

const searchOpen = ref(false)
</script>

<template>
  <header class="flex items-center justify-between border-b border-neutral-900 px-4 py-3.5 sm:px-7">
    <AppLogo
      :label="false"
      :size="26"
    />

    <span class="ml-2.5 min-w-0 truncate font-mono text-[13px] text-neutral-500">
      {{ app }}<template v-for="c in crumbs" :key="c"> / <span class="text-neutral-50">{{ c }}</span></template>
    </span>

    <nav class="ml-auto flex items-center gap-2">
      <SearchTrigger
        v-model:open="searchOpen"
        :scope="scope"
      />
      <UButton
        to="/docs"
        variant="outline"
        color="neutral"
        class="hidden rounded-full sm:inline-flex"
      >
        Docs
      </UButton>
      <UButton
        to="https://github.com/foxws"
        target="_blank"
        variant="outline"
        color="neutral"
        class="hidden rounded-full sm:inline-flex"
      >
        GitHub ↗
      </UButton>
      <UButton
        icon="i-lucide-menu"
        variant="outline"
        color="neutral"
        class="rounded-full sm:hidden"
        square
        aria-label="Menu"
      />
    </nav>

    <CommandPalette
      v-model:open="searchOpen"
      :scope="scope"
    />
  </header>
</template>
