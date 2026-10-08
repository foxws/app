export function useMonogram() {
  /** Up to two initials, e.g. "LP" for "Laravel Podman", standing in until projects have logos. */
  const formatMonogram = (name: string): string =>
    name
      .split(/\s+/)
      .slice(0, 2)
      .map((word) => word.charAt(0))
      .join('')
      .toUpperCase()

  return {
    formatMonogram,
  }
}
