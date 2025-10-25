import { fileURLToPath } from 'node:url'
import { configDefaults, defineConfig, mergeConfig } from 'vitest/config'
import viteConfig from './vite.config.mjs'

export default mergeConfig(
  viteConfig,
  defineConfig({
    plugins: [
      {
        name: 'vitest-plugin-beforeall',
        config: () => ({
          test: {
            setupFiles: [fileURLToPath(new URL('./vitest/beforeAll.mts', import.meta.url))]
          }
        })
      } as any
    ],
    test: {
      globals: true,
      setupFiles: [fileURLToPath(new URL('./vitest/setup.mts', import.meta.url))],
      environment: 'jsdom',
      exclude: [...configDefaults.exclude, 'e2e/**'],
      root: fileURLToPath(new URL('./', import.meta.url)),
    }
  })
)
