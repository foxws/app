import inertia from '@inertiajs/vite'
import { wayfinder } from '@laravel/vite-plugin-wayfinder'
import ui from '@nuxt/ui/vite'
import tailwindcss from '@tailwindcss/vite'
import vue from '@vitejs/plugin-vue'
import laravel from 'laravel-vite-plugin'
import { google } from 'laravel-vite-plugin/fonts'
import { fileURLToPath, URL } from 'node:url'
import { defineConfig, lazyPlugins, loadEnv } from 'vite-plus'

// https://vite.dev/config/
export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), '')

  return {
    plugins: lazyPlugins(() => [
      laravel({
        input: ['resources/css/app.css', 'resources/js/app.ts'],
        refresh: true,
        fonts: [
          google('Space Grotesk', { weights: ['400', '500', '600', '700'], alias: 'space-grotesk' }),
          google('JetBrains Mono', { weights: ['400', '500', '700'], alias: 'jetbrains-mono' }),
        ],
      }),
      inertia({
        ssr: {
          port: 13714,
          cluster: true,
        },
      }),
      tailwindcss(),
      vue({
        template: {
          transformAssetUrls: {
            base: null,
            includeAbsolute: false,
          },
        },
      }),
      wayfinder({
        formVariants: true,
      }),
      ui({
        router: 'inertia',
        // Content components (UContentNavigation/UContentToc/UContentSurround) are only
        // wired up when @nuxt/content is present — force them since docs nav/toc/surround
        // here come from Inertia props instead, not a Content collection. The Vite
        // plugin's public NuxtUIOptions type omits `content` (it's Nuxt-module-only in
        // the types), but the plugin itself still reads it at runtime.
        // @ts-expect-error -- see comment above
        content: true,
        ui: {
          colors: {
            primary: 'identity',
            secondary: 'zinc',
            neutral: 'zinc',
          },
          input: {
            slots: {
              root: 'w-full',
            },
          },
          inputDate: {
            slots: {
              base: 'w-full',
            },
          },
          textarea: {
            slots: {
              root: 'w-full',
            },
          },
          pageHero: {
            slots: {
              headline: 'font-mono text-xs font-normal tracking-[.18em] text-identity-500 uppercase',
            },
          },
        },
      }),
    ]),
    server: {
      host: '0.0.0.0',
      port: 5173,
      strictPort: true,
      hmr: { host: env.VITE_HMR_HOST, clientPort: 443, protocol: 'wss' },
      watch: {
        ignored: [
          '**/.agents/**',
          '**/.claude/**',
          '**/vendor/**',
          '**/storage/framework/views/**',
          '**/storage/logs/**',
        ],
      },
    },
    lint: {
      plugins: ['eslint', 'typescript', 'unicorn', 'oxc', 'vue', 'vitest'],
      jsPlugins: [
        {
          name: 'vite-plus',
          specifier: 'vite-plus/oxlint-plugin',
        },
      ],
      rules: {
        'vite-plus/prefer-vite-plus-imports': 'error',
      },
      overrides: [
        {
          files: ['resources/js/**/__tests__/**'],
          rules: {
            'typescript/unbound-method': 'off',
          },
        },
      ],
      options: {
        denyWarnings: true,
        typeAware: true,
      },
    },
    fmt: {
      semi: false,
      singleQuote: true,
      singleAttributePerLine: true,
      printWidth: 120,
      sortPackageJson: false,
      sortTailwindcss: {
        stylesheet: 'resources/css/app.css',
        functions: ['cva', 'clsx', 'ui', ':ui'],
        attributes: ['class', 'className', 'ui', ':ui'],
      },
      ignorePatterns: [
        '.agents',
        '.claude',
        '.github',
        '.mcp.json',
        '/AGENTS.md',
        '/CLAUDE.md',
        'boost.json',
        'skills-lock.json',
        '/public',
      ],
    },
    test: {
      include: ['resources/js/**/*.{test,spec}.ts'],
    },
    resolve: {
      alias: {
        '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
        '@images': fileURLToPath(new URL('./resources/images', import.meta.url)),
        '~': fileURLToPath(new URL('./node_modules', import.meta.url)),
        '!': fileURLToPath(new URL('./vendor', import.meta.url)),
      },
    },
    ssr: {
      // @nuxt/ui relies on Vite-only virtual modules (e.g. `#imports`) that
      // only resolve while bundling, so it must never be externalized for SSR.
      noExternal: ['@nuxt/ui'],
    },
    build: {
      chunkSizeWarningLimit: 1000,
      rollupOptions: {
        output: {
          manualChunks(id) {
            const chunk = (name: string, packages: string[]) =>
              packages.some((pkg) => id.includes(`node_modules/${pkg}`)) ? name : undefined

            return (
              chunk('icons', ['@iconify']) ??
              chunk('ui', ['@nuxt/ui', '@nuxt/icon', 'reka-ui', '@internationalized']) ??
              chunk('core', ['vue', '@inertiajs', '@vueuse']) ??
              chunk('broadcasting', ['pusher-js', 'laravel-echo'])
            )
          },
        },
      },
    },
  }
})
