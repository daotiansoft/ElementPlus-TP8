import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";
import router from "./router"; // 导入路由
import "./router/routes"; // 导入路由

import "@/styles/reset.css"; // ✅ 引入重置样式
import "@/styles/search.css";
import "@/styles/theme.css";

// 网站配置加载前使用环境变量兜底，加载后由后台 site_name 更新标题
const appTitle = import.meta.env.VITE_APP_TITLE;
if (appTitle) {
  document.title = appTitle;
}

const app = createApp(App);

const pinia = createPinia();
app.use(pinia);
app.use(router);
app.mount("#app");
