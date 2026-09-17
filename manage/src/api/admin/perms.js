import { BaseApi } from "@/utils/baseApi";

class Api extends BaseApi {
  constructor() {
    super("/super/perms/");
  }
}

const api = new Api();

export default api;
