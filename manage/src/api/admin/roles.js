import request from "@/utils/request";

const rolesApi = {
  getItems(data) {
    return request({
      url: "/super/roles/items",
      method: "post",
      data,
    });
  },
  add(data) {
    return request({
      url: "/super/roles/add",
      method: "post",
      data,
    });
  },
  edit(data) {
    return request({
      url: "/super/roles/edit",
      method: "post",
      data,
    });
  },
  del(data) {
    return request({
      url: "/super/roles/del",
      method: "post",
      data,
    });
  },
  all() {
    return request({
      url: "/super/roles/all",
    });
  },
};

export default rolesApi;
