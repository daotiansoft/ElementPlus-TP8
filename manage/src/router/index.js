// src/router/index.js
import {
  createRouter,
  createWebHistory,
  createWebHashHistory,
} from "vue-router";
import Layout from "@/layouts/index.vue";
import { useUserStore } from "@/store/modules/user";
import transformRoutes from "@/router/routes";

export const commonRoutes = [
  {
    path: "/",
    component: Layout,
    redirect: "/dashboard",
    children: [
      {
        path: "dashboard",
        name: "控制台",
        component: () => import("@/views/Dashboard.vue"),
        meta: { title: "控制台", icon: "Odometer" },
      },
    ],
  },
  {
    path: "/login",
    name: "Login",
    component: () => import("@/views/Login.vue"),
    hidden: true,
  },
];

const router = createRouter({
  // history: createWebHistory(), // HTML5 模式（无 # 号）
  history: createWebHashHistory(), // Hash 模式（有 # 号）
  routes: [...commonRoutes],

  // 可选：滚动行为
  // scrollBehavior(to, from, savedPosition) {
  //   if (savedPosition) {
  //     return savedPosition; // 浏览器前进后退时保持滚动位置
  //   } else {
  //     return { top: 0 }; // 每次路由切换滚动到顶部
  //   }
  // },
});

const addRoutes = async () => {
  const userStore = useUserStore();
  const items = transformRoutes(await userStore.getRoutesItems());
  items.forEach((item) => {
    if (item.component == undefined || item.component == "") {
      item.component = Layout;
    }
    router.addRoute(item);
  });
  router.addRoute({
    path: "/:pathMatch(.*)*",
    name: "NotFound",
    component: () => import("@/views/NotFound.vue"),
    hidden: true,
  });
};

// 白名单（不需要登录验证的路由）
const whiteList = ["/login"];
let hasAddedRoutes = false;
router.beforeEach(async (to, from, next) => {
  const hasToken = localStorage.getItem("token");
  if (hasToken) {
    if (to.path === "/login") {
      next("/");
    } else {
      const routeExists = router.getRoutes().some((r) => r.path === to.path);
      if (!routeExists) {
        await addRoutes();
        next({ ...to, replace: true });
        return;
      }
      next();
    }
  } else {
    if (whiteList.includes(to.path)) {
      next();
    } else {
      next("/login");
    }
  }
});

export default router;
