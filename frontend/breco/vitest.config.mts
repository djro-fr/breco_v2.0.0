import { fileURLToPath } from 'node:url'
import { defineConfig, mergeConfig } from 'vitest/config'
import viteConfig from './vite.config.mjs'

export default mergeConfig(
  viteConfig,
  defineConfig({
    test: {
      globals: true,
      setupFiles: [
        fileURLToPath(new URL('./vitest/setup-global.ts', import.meta.url)),
        fileURLToPath(new URL('./vitest/setup.ts', import.meta.url)),
      ],
      environment: 'jsdom',
      environmentOptions: {
        jsdom: {
          url: 'http://localhost/',
        },
      },
      exclude: ['**/node_modules/**', '**/e2e/**'],
      root: fileURLToPath(new URL('./', import.meta.url)),
      coverage: {
        provider: 'v8',
      },
    },
  })
)
