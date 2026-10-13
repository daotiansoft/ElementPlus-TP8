import { BaseApi } from "@/utils/baseApi";

class Api extends BaseApi {
  constructor() {
    super("/admin/perms/");
  }
  createCurd(data) {
    return this.request({
      url: "createCurd",
      method: "post",
      data,
    });
  }
}

const api = new Api();

export default api;
