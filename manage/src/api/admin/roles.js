import { BaseApi } from "@/utils/baseApi";

class Api extends BaseApi {
  constructor() {
    super("/admin/roles/");
  }
}

const api = new Api();

export default api;
