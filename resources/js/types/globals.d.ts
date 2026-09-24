import type { CollectionItem, FlashData, OptionItem, User } from '@/types'
import type { Page } from '@inertiajs/vue3'
import type { SelectMenuItem } from '@nuxt/ui'

declare module '@inertiajs/core' {
  export interface InertiaConfig {
    readonly flashDataType: FlashData
  }

  export interface PageProps {
    readonly app: string
    readonly nonce: string
    readonly locale: string
    readonly locales: OptionItem[] | undefined
    readonly languages: OptionItem[] | undefined
    readonly tags: OptionItem[] | undefined
    readonly search: string | null | undefined
    readonly auth: User | undefined
    readonly collections: CollectionItem[] | undefined
    readonly unread: number
  }
}

declare module '@inertiajs/vue3' {
  export declare function usePage<T>(): Page<T>
}
