import request from "@/utils/request";

const configList = {
  getItems(data) {
    return request({
      url: "/super/configList/items",
      method: "post",
      data,
    });
  },
  add(data) {
    return request({
      url: "/super/configList/add",
      method: "post",
      data,
    });
  },
  edit(data) {
    return request({
      url: "/super/configList/edit",
      method: "post",
      data,
    });
  },
  del(data) {
    return request({
      url: "/super/configList/del",
      method: "post",
      data,
    });
  },
};

export default configList;
