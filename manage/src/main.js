import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";
import router from "./router"; // 导入路由
import "./router/routes"; // 导入路由

import "@/styles/reset.css"; // ✅ 引入重置样式

const app = createApp(App);

const pinia = createPinia();
app.use(pinia);
app.use(router);
app.mount("#app");
