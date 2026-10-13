import { BaseApi } from "@/utils/baseApi";

class Api extends BaseApi {
  constructor() {
    super("/admin/balance/");
  }
}

const api = new Api();

export default api;
