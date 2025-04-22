import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import { config }  from 'dotenv'

import vue from '@vitejs/plugin-vue'

config({
  path: '../.env'
})

export default defineConfig({
  server: {
    proxy: {
      '/api': {
        target: 'http://127.0.0.1:8000',
      },
    },
  },
  plugins: [
    vue(),
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url))
    }
  }
})
