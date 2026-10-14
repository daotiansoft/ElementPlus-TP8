import { BaseApi } from "@/utils/baseApi";

class Api extends BaseApi {
  constructor() {
    super("/admin/config/");
  }
  getItems() {
    return this.request({
      url: "items",
    });
  }
  postItems(data) {
    return this.request({
      url: "save",
      method: "post",
      data,
    });
  }
  clearCache() {
    return this.request({
      url: "clearCache",
    });
  }
}

const api = new Api();

export default api;
