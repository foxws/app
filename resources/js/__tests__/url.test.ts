import { useUrlFormat } from '@/composables/url'
import { expect, test } from 'vite-plus/test'

const { formatDestination } = useUrlFormat()

test('shows the host and path of a link, without the scheme', () => {
  expect(formatDestination('https://github.com/francoism90/stry')).toBe('github.com/francoism90/stry')
})

test('drops a trailing slash, including on a bare domain', () => {
  expect(formatDestination('https://foxws.nl/')).toBe('foxws.nl')
  expect(formatDestination('https://github.com/francoism90/stry/')).toBe('github.com/francoism90/stry')
})

test('returns a relative link as it is', () => {
  expect(formatDestination('/stry')).toBe('/stry')
})
