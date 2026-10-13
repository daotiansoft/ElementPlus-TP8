import { BaseApi } from "@/utils/baseApi";

class Api extends BaseApi {
  constructor() {
    super("/user/balance/");
  }

  // 当前用户余额
  getInfo() {
    return this.request({ url: "info", method: "get" });
  }
}

const api = new Api();

export default api;
