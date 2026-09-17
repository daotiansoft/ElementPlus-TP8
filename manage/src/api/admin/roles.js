import { BaseApi } from "@/utils/baseApi";

class Api extends BaseApi {
  constructor() {
    super("/super/roles/");
  }
}

const api = new Api();

export default api;
