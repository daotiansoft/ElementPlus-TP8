import Layout from "@/layouts/index.vue";

const routesMap = {
  "/admin/config": {
    component: () => import("@/layouts/index.vue"),
    meta: { title: "设置", icon: "Setting" },
    hidden: false,
  },
  "/admin/config/system": {
    name: "系统设置",
    component: () => import("@/views/admin/config/system.vue"),
    meta: { title: "系统设置" },
    hidden: false,
  },
  "/admin/config/editor": {
    name: "富文本",
    component: () => import("@/views/admin/config/editor.vue"),
    meta: { title: "富文本" },
    hidden: false,
  },

  "/admin/account": {
    component: () => import("@/layouts/index.vue"),
    meta: { title: "账号管理", icon: "User" },
    hidden: false,
  },
  "/admin/account/member": {
    name: "账号",
    component: () => import("@/views/admin/account/member.vue"),
    meta: { title: "账号" },
    hidden: false,
  },
  "/admin/account/appList": {
    name: "API管理",
    component: () => import("@/views/admin/account/appList.vue"),
    meta: { title: "API管理" },
    hidden: false,
  },
  "/admin/account/roles": {
    name: "角色管理",
    component: () => import("@/views/admin/account/roles.vue"),
    meta: { title: "角色管理" },
    hidden: false,
  },
  "/admin/account/perms": {
    name: "权限节点",
    component: () => import("@/views/admin/account/perms.vue"),
    meta: { title: "权限节点" },
    hidden: false,
  },

  "/center": {
    component: () => import("@/layouts/index.vue"),
    meta: { title: "个人中心", icon: "User" },
    hidden: true,
  },
  "/center/password": {
    name: "Password",
    component: () => import("@/views/center/Password.vue"),
    meta: { title: "修改密码" },
    hidden: true,
  },
};

const transformRoutes = (paths) => {
  return paths.map((path) => {
    let routeCopy = routesMap[path.name];
    if (routeCopy) {
      routeCopy["path"] = path.name;
      if (path.children && path.children.length > 0) {
        routeCopy["children"] = transformRoutes(path.children);
      }
      return routeCopy;
    } else {
      return false;
    }
  });
};

export default transformRoutes;
