export function useUrlFormat() {
  /** Where a link goes, without the scheme or a trailing slash, e.g. "github.com/francoism90/stry". */
  const formatDestination = (href: string): string => {
    try {
      const url = new URL(href)

      return `${url.host}${url.pathname}`.replace(/\/$/, '')
    } catch {
      return href
    }
  }

  return {
    formatDestination,
  }
}
