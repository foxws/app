<script setup lang="ts">
import { computed } from 'vue'

const props = withDefaults(
  defineProps<{
    name: string
    type?: string | null
    desc: string
    status?: string | null
    href?: string | null
  }>(),
  {
    type: null,
    status: null,
    href: null,
  },
)

const statusColor: Record<string, string> = {
  active: 'bg-success-500',
  ongoing: 'bg-warning-500',
}

const isExternal = computed(() => props.href?.startsWith('http') ?? false)
</script>

<template>
  <ULink
    :to="href ?? undefined"
    :target="isExternal ? '_blank' : undefined"
    raw
    class="group relative flex flex-col gap-1.75 py-4"
  >
    <div class="flex items-center justify-between gap-2.5">
      <div class="flex items-center gap-2.5">
        <span class="font-mono text-sm font-semibold text-neutral-50">{{ name }}</span>
        <span
          v-if="type"
          class="rounded-full border border-neutral-800 px-2 py-0.5 font-mono text-[10px] tracking-wider text-neutral-400 uppercase"
          >{{ type }}</span
        >
      </div>
      <UIcon
        name="i-lucide-arrow-up-right"
        class="size-4 shrink-0 text-neutral-600 group-hover:text-neutral-400"
      />
    </div>

    <span class="font-sans text-sm text-neutral-400">{{ desc }}</span>

    <span
      v-if="status"
      class="flex items-center gap-1.5 font-mono text-xs text-neutral-500"
    >
      <span :class="['size-1.5 rounded-full', statusColor[status] ?? 'bg-neutral-600']" />
      {{ status }}
    </span>
  </ULink>
</template>
