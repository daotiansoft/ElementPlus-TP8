import request from "@/utils/request";

const member = {
  getItems(data) {
    return request({
      url: "/super/perms/items",
      method: "post",
      data,
    });
  },
  add(data) {
    return request({
      url: "/super/perms/add",
      method: "post",
      data,
    });
  },
  edit(data) {
    return request({
      url: "/super/perms/edit",
      method: "post",
      data,
    });
  },
  del(data) {
    return request({
      url: "/super/perms/del",
      method: "post",
      data,
    });
  },
  types() {
    return request({
      url: "/super/perms/types",
    });
  },
};

export default member;
