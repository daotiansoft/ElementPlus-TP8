import request from "@/utils/request";

const configGroup = {
  getItems(data) {
    return request({
      url: "/super/configGroup/items",
      method: "post",
      data,
    });
  },
  add(data) {
    return request({
      url: "/super/configGroup/add",
      method: "post",
      data,
    });
  },
  edit(data) {
    return request({
      url: "/super/configGroup/edit",
      method: "post",
      data,
    });
  },
  del(data) {
    return request({
      url: "/super/configGroup/del",
      method: "post",
      data,
    });
  },
  getOptions() {
    return request({
      url: "/super/configGroup/getOptions",
    });
  },
};

export default configGroup;
