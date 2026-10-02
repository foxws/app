const compactFormatter = new Intl.NumberFormat('en', { notation: 'compact', maximumFractionDigits: 1 })
const fullFormatter = new Intl.NumberFormat('en')

export function useNumberFormat() {
  const formatCompact = (value: number): string => compactFormatter.format(value)
  const formatFull = (value: number): string => fullFormatter.format(value)

  return {
    formatCompact,
    formatFull,
  }
}
