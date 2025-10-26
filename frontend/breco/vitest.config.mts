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
          url: process.env.VITE_API_URL || 'http://localhost/',
        },
      },
      include: [
        'src/**/__tests__/unit/**/*.spec.ts',
        'src/**/__tests__/ui/**/*.spec.ts',
        'src/**/__tests__/integration/**/*.spec.ts',
        'src/**/__tests__/e2e/**/*.spec.ts',
      ],
      exclude: ['**/node_modules/**'],
      root: fileURLToPath(new URL('./', import.meta.url)),
      coverage: {
        provider: 'v8',
        reporter: ['text', 'json', 'html', 'lcov'],
        exclude: [
          'node_modules/',
          'src/**/__tests__/',
        ]
      }
    },
  })
)
