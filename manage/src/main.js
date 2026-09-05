import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";
//import ElementPlus from "element-plus";
//import zhCn from "element-plus/es/locale/lang/zh-cn";
import router from "./router"; // 导入路由
import "./router/routes"; // 导入路由

//import "element-plus/dist/index.css";
import "@/styles/reset.css"; // ✅ 引入重置样式
import * as ElementPlusIconsVue from "@element-plus/icons-vue";

const app = createApp(App);
// app.use(ElementPlus, {
//   locale: zhCn,
// });

for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
  app.component(key, component);
}

const pinia = createPinia();
app.use(pinia);
app.use(router);
app.mount("#app");
