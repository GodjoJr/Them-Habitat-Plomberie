import path from 'path'
import { defineConfig } from 'vite'
import sassGlobImports from 'vite-plugin-sass-glob-import'

export default defineConfig({
  base: './',
  server: {
    cors: {
      origin: "*"
    },
    strictPort: true
  },
  build: {
    manifest: 'manifest.json',
    assetsDir: '.',
    outDir: `assets`,
    emptyOutDir: true,
    sourcemap: true,
    minify: true,
    rollupOptions: {
      input: [
        'resources/js/app.js'
      ],
      output: {
        entryFileNames: '[name].js',
        assetFileNames: '[name].[ext]'
      }
    }
  },
  css: {
    preprocessorOptions:{
      scss: {
        api: 'modern-compiler'
      }
    }
  },
  plugins: [
    sassGlobImports(),
    {
      handleHotUpdate({ file, server }) {
        if (file.endsWith('.php')) server.ws.send({ type: 'full-reload', path: '*' })
      }
    }
  ],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, 'resources')
    }
  }
})