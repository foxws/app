import type { AvatarProps, BadgeProps, SelectItem } from '@nuxt/ui'

export type DocsNavItem = {
  title: string
  path?: string
  exact?: boolean
  children?: { title: string; path: string; exact?: boolean }[]
}

export type DocsTocItem = {
  id: string
  text: string
  children: { id: string; text: string }[]
}

export type DocsSurroundLink = {
  title: string
  path: string
}

export type DocsPackageInfo = {
  version?: string
  requires?: string
  laravel?: string
  runtime?: string
  licence?: string
}

export type DocsUsedBy = {
  name: string
  desc?: string
  href: string
}

export type DocsVersion = {
  name: string
  is_default: boolean
}

export type DocsProjectRef = {
  name: string
  slug: string
  href: string
}

export type DocsProject = {
  key: string
  name: string
  slug: string
  eyebrow: string
  lead: string
  install: string
  overview: { html: string; toc: DocsTocItem[] } | null
  nav: DocsNavItem[]
  versions: DocsVersion[]
  version: string | null
  github: string | null
  package: DocsPackageInfo | null
  used_by: DocsUsedBy | null
  get_started: string | null
}

export type DocsDocument = {
  project: DocsProjectRef
  title: string
  html: string
  toc: DocsTocItem[]
  nav: DocsNavItem[]
  surround: (DocsSurroundLink | null)[]
}

export type EchoConfig = {
  readonly key: string
  readonly host: string
  readonly port: number
  readonly scheme: string
}

export type FlashType = 'success' | 'error' | 'warning' | 'info' | 'primary'

export type FlashData = {
  readonly title?: string
  readonly description?: string
  readonly type?: FlashType
}

export type OptionItem = SelectItem & {
  label: string
  value: string | number | boolean | null
  disabled?: boolean
}

export type QueryValue = string | number | boolean | null

export type QueryFilter = Record<string, QueryValue>

export type Model = {
  id: string
  created_at: string
  updated_at: string
}

export type ModelResource = Model & {
  subject?: string
  name?: string
  label?: string
  slug?: string
}

export type ModelState = {
  name: string
  label: string
  icon: string
  color: BadgeProps['color']
}

export type Paginator = {
  data: Model[] | undefined
  links: {
    first: string | undefined
    last: string | undefined
    prev: string | undefined
    next: string | undefined
  }
  meta: {
    current_page: number
    current_page_url: string
    from: number | undefined
    path: string
    per_page: number
    to: number | undefined
    total: number
  }
}

export type User = Model & {
  name: string
  email?: string
  avatar?: AvatarProps['src'] | null
  roles?: string[] | null
  permissions?: string[] | null
  settings?: UserSettings
  videos_count?: number
  state?: ModelState
  email_verified_at?: string | null
  deleted_at?: string | null
}

export type UserCollection = Omit<Paginator, 'data'> & {
  data: User[] | undefined
}

export type UserSettings = {
  general: GeneralSettings
  appearance: AppearanceSettings
  player: PlayerSettings
}

export type GeneralSettings = {
  timezone: string
  locale: string
  language: string
  date_format: string
  time_format: string
}

export type Media = Model & {
  name: string
  url?: string | null
  file_name: string
  mime_type: string
  size: number
  file_size: string
  collection_name: string
  disk: string
  conversions_disk: string
  codec?: string
  resolution?: string
  bitrate?: string
  custom_properties?: MediaCustomProperties | null
  generated_conversions?: Record<string, unknown> | null
  responsive_images?: Record<string, unknown> | null
}

export type MediaCollection = Omit<Paginator, 'data'> & {
  data: Media[] | undefined
}
