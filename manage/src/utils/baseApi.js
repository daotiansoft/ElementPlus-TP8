// @/utils/baseApi.js
import request from "@/utils/request";

export class BaseApi {
  /**
   * @param {string} urlPre 接口前缀，子类通过 super(urlPre) 传入
   */
  constructor(urlPre) {
    this.urlPre = urlPre;
  }

  /**
   * 统一请求入口，方便以后加拦截、loading、签名等
   * @param {object} options { url, method, data, params, ... }
   */
  request(options) {
    const { url, method = "post", ...rest } = options;
    return request({
      method,
      url: this.urlPre + url,
      ...rest,
    });
  }

  // ---------- 公共 CRUD ----------

  getItems(data) {
    return this.request({ url: "items", method: "post", data });
  }

  add(data) {
    return this.request({ url: "add", method: "post", data });
  }

  edit(data) {
    return this.request({ url: "edit", method: "post", data });
  }

  del(data) {
    return this.request({ url: "del", method: "post", data });
  }

  getOptions() {
    return this.request({ url: "getOptions", method: "get" });
  }
}
