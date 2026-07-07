// vite.config.js
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'path'
import AutoImport from 'unplugin-auto-import/vite'
import Components from 'unplugin-vue-components/vite'
import { ElementPlusResolver } from 'unplugin-vue-components/resolvers'

export default defineConfig({
  // 插件配置
  plugins: [
    vue(),
    AutoImport({
      resolvers: [ElementPlusResolver()],
    }),
    Components({
      resolvers: [ElementPlusResolver()],
    }),
  ],
  publicDir: 'public',
  // 路径别名
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
      'components': path.resolve(__dirname, './src/components'),
      'views': path.resolve(__dirname, './src/views'),
      'utils': path.resolve(__dirname, './src/utils')
    }
  },
  
  // 开发服务器配置
  server: {
    port: 3000,           // 端口号
    open: true,           // 自动打开浏览器
    host: '0.0.0.0',      // 允许局域网访问
    https: false,         // 是否启用 https
  },
  
  // 构建配置
  build: {
    outDir: 'dist',           // 输出目录
    assetsDir: 'assets',      // 静态资源目录
    sourcemap: false,         // 是否生成 sourcemap
    minify: 'esbuild',        // 压缩方式：'esbuild' | 'terser'
    rollupOptions: {
      output: {
        // 代码分割
        manualChunks: {
          vue: ['vue', 'vue-router', 'pinia']
        }
      }
    }
  },
  
  
  // 环境变量
  define: {
    __APP_VERSION__: JSON.stringify('1.0.0')
  }
})