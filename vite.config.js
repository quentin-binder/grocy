import { defineConfig } from 'vite';
import { resolve } from 'path';

export default defineConfig({
  root: '.',
  base: '/build/',

  build: {
    outDir: 'public/build',
    manifest: true,
    rollupOptions: {
      input: {
        app: resolve(__dirname, 'resources/js/app.js'),
        css: resolve(__dirname, 'resources/css/app.css'),
      },
    },
    emptyOutDir: true,
  },

  server: {
    port: 5173,
    strictPort: true,
    cors: true,
    origin: 'http://localhost:5173',
  },

  css: {
    devSourcemap: true,
  },
});
