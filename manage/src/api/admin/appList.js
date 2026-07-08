import request from "@/utils/request";

const appList = {
  getItems(data) {
    return request({
      url: "/super/appList/items",
      method: "post",
      data,
    });
  },
  add(data) {
    return request({
      url: "/super/appList/add",
      method: "post",
      data,
    });
  },
  edit(data) {
    return request({
      url: "/super/appList/edit",
      method: "post",
      data,
    });
  },
  del(data) {
    return request({
      url: "/super/appList/del",
      method: "post",
      data,
    });
  },
};

export default appList;
