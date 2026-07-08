import request from "@/utils/request";

const member = {
  getItems(data) {
    return request({
      url: "/super/member/items",
      method: "post",
      data,
    });
  },
  add(data) {
    return request({
      url: "/super/member/add",
      method: "post",
      data,
    });
  },
  edit(data) {
    return request({
      url: "/super/member/edit",
      method: "post",
      data,
    });
  },
  del(data) {
    return request({
      url: "/super/member/del",
      method: "post",
      data,
    });
  },
};

export default member;
