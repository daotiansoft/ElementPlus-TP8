import { BaseApi } from "@/utils/baseApi";

class Api extends BaseApi {
  constructor() {
    super("/admin/member/");
  }

  // 调整账号余额
  adjust(data) {
    return this.request({ url: "adjust", method: "post", data });
  }
}

const api = new Api();

export default api;
