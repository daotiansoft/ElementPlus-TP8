import { BaseApi } from "@/utils/baseApi";

class Api extends BaseApi {
  constructor() {
    super("/common/overt/");
  }
  init() {
    return this.request({
      url: "init",
    });
  }
  getCaptcha() {
    return this.request({
      url: "captcha",
    });
  }
  postLogin(data) {
    return this.request({
      url: "login",
      method: "post",
      data,
    });
  }
}

const api = new Api();

export default api;
