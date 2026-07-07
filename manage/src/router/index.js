// src/router/index.js
import { createRouter, createWebHistory } from 'vue-router'
import Layout from '@/layouts/index.vue'

// 1. 定义路由规则
const routes = [
  {
    path: '/',
    component: Layout,
    redirect: '/dashboard',
    children: [
      {
        path: 'dashboard',
        name: '控制台',
        component: () => import('@/views/Dashboard.vue'),
        meta: { title: '控制台', icon: 'Odometer' }
      }, 
    ]
  },
  {
    path: '/admin/config',
    component: Layout,
    meta: { title: '设置', icon: 'Setting' },
    hidden: false,
    children: [
      {
        path: '/admin/config/system',
        name: '系统',
        component: () => import('@/views/admin/config/system.vue'),
        meta: { title: '系统设置' },
        hidden: false
      },
      {
        path: '/admin/config/editor',
        name: '富文本',
        component: () => import('@/views/admin/config/editor.vue'),
        meta: { title: '富文本'},
        hidden: false
      },  
      {
        path: '/admin/config/other',
        name: '其他',
        component: () => import('@/views/admin/config/other.vue'),
        meta: { title: '其他设置'},
        hidden: false
      }, 
    ]
  },
  {
    path: '/center',
    component: Layout,
    meta: { title: '个人中心', icon: 'User' },
    hidden: true,
    children: [
      {
        path: '/center/password',
        name: 'Password',
        component: () => import('@/views/center/Password.vue'),
        meta: { title: '修改密码' },
        hidden: true
      },
    ]
  },
  {
    // 404 页面（必须放在最后）
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('@/views/NotFound.vue'),
    hidden: true
  },
  {
    path: '/login',
    name: 'Login',
    component: () => import('@/views/Login.vue'),
    hidden: true
  },
  
]

// 2. 创建路由实例
const router = createRouter({
  history: createWebHistory(),  // HTML5 模式（无 # 号）
  // history: createWebHashHistory(), // Hash 模式（有 # 号）
  routes,
  
  // 可选：滚动行为
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition  // 浏览器前进后退时保持滚动位置
    } else {
      return { top: 0 }     // 每次路由切换滚动到顶部
    }
  }
})

// 3. 导出路由实例
export default router