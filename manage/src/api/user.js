import { BaseApi } from "@/utils/baseApi";

class Api extends BaseApi {
  constructor() {
    super("/common/account/");
  }
  getUserInfo() {
    return this.request({
      url: "info",
    });
  }
  logout() {
    return this.request({
      url: "logout",
    });
  }
  postPassword(data) {
    return this.request({
      url: "password",
      method: "post",
      data,
    });
  }
  getNotice() {
    return this.request({
      url: "notice",
    });
  }
  getUploadInfo() {
    return this.request({
      url: "uploadinfo",
    });
  }
  uploadFile(data) {
    return this.request({
      url: "upload",
      method: "post",
      data,
      headers: {
        "Content-Type": "multipart/form-data",
      },
    });
  }
  getRoutes() {
    return this.request({
      url: "getRoutes",
    });
  }
}

const api = new Api();

export default api;
