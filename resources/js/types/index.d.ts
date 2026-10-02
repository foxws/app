import type { SelectItem } from '@nuxt/ui'

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

export type DocsPackageSummary = {
  name: string
  slug: string
  path: string
  role: string | null
  desc: string
  version?: string
  downloads: number | null
}

export type DocsPackageGroup = {
  name: string | null
  packages: DocsPackageSummary[]
}

export type DocsSideProjectSummary = {
  name: string
  slug: string
  type: string | null
  desc: string
  status: string | null
  href: string | null
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
  install: string | null
  overview: { html: string; toc: DocsTocItem[] } | null
  nav: DocsNavItem[]
  versions: DocsVersion[]
  version: string | null
  source: string | null
  package: DocsPackageInfo | null
  used_by: DocsUsedBy | null
  get_started: string | null
  surround: (DocsSurroundLink | null)[]
}

export type DocsDocument = {
  project: DocsProjectRef
  title: string
  description: string
  html: string
  toc: DocsTocItem[]
  nav: DocsNavItem[]
  surround: (DocsSurroundLink | null)[]
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

export type AuthUser = {
  id: number
  name: string
  email: string
}
