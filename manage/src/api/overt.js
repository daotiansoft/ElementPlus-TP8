import { createApi } from "@/utils/baseApi";

export default createApi("/common/overt/", (request) => ({
  init: async () => {
    const website = await request({ url: "init" });
    const siteName = website?.site_name?.trim();
    document.title = siteName || import.meta.env.VITE_APP_TITLE || "管理面板";
    return website;
  },
  getCaptcha: () => request({ url: "captcha" }),
  postLogin: (data) => request({ url: "login", method: "post", data }),
}));
