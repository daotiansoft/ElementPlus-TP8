import { BaseApi } from "@/utils/baseApi";

class Api extends BaseApi {
  constructor() {
    super("/super/editor/");
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
}

const api = new Api();

export default api;
