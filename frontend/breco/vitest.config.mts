import { fileURLToPath } from 'node:url'
import { configDefaults, defineConfig, mergeConfig } from 'vitest/config'
import viteConfig from './vite.config.mjs'

export default mergeConfig(
  viteConfig,
  defineConfig({
    test: {
      globals: true,
      setupFiles: [
        fileURLToPath(new URL('./vitest/setup.ts', import.meta.url)),
      ],
      environment: 'jsdom',
      environmentOptions: {
        jsdom: {
          url: process.env.VITE_API_URL || 'http://37.59.101.232:8081/api',
        },
      },
      exclude: [...configDefaults.exclude, 'e2e/*'],
      root: fileURLToPath(new URL('./', import.meta.url)),
      coverage: {
        provider: 'v8'
      }
    }
  })
)
