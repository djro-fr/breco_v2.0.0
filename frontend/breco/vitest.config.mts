import { fileURLToPath } from 'node:url'
import { configDefaults, defineConfig, mergeConfig } from 'vitest/config'
import viteConfig from './vite.config.mjs'

export default mergeConfig(
  (viteConfig as ({ mode }: { mode: string }) => Record<string, any>)({ mode: 'test' }),
  defineConfig({
    plugins: [
      {
        name: 'vitest-plugin-beforeall',
        config: () => ({
          test: {
            setupFiles: [fileURLToPath(new URL('./vitest/beforeAll.ts', import.meta.url))]
          }
        })
      } as any
    ],
    test: {
      globals: true,
      setupFiles: [fileURLToPath(new URL('./vitest/setup.ts', import.meta.url))],
      environment: 'jsdom',
      exclude: [...configDefaults.exclude, 'e2e/*'],
      root: fileURLToPath(new URL('./', import.meta.url)),
      coverage: {
        provider: 'v8'
      }
    }
  })
)
