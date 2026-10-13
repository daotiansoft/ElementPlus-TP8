import { BaseApi } from "@/utils/baseApi";

class Api extends BaseApi {
  constructor() {
    super("/admin/rolePerms/");
  }
  save(data) {
    return this.request({
      url: "save",
      method: "post",
      data,
    });
  }
}

const api = new Api();

export default api;
