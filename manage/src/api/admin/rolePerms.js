import request from "@/utils/request";

const rolePerms = {
  save(data) {
    return request({
      url: "/super/rolePerms/save",
      method: "post",
      data,
    });
  },
};

export default rolePerms;
