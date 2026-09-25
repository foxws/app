import type { AuthUser, FlashData, OptionItem } from '@/types'
import type { Page } from '@inertiajs/vue3'

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
    readonly auth: AuthUser | null
  }
}

declare module '@inertiajs/vue3' {
  export declare function usePage<T>(): Page<T>
}
