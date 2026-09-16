const routesMap = {
  "/admin/system": {
    meta: { title: "系统", icon: "Setting" },
    hidden: false,
  },
  "/admin/system/config": {
    name: "设置",
    component: () => import("@/views/admin/system/config.vue"),
    meta: { title: "设置" },
    hidden: false,
  },
  "/admin/system/configGroup": {
    name: "设置分组",
    component: () => import("@/views/admin/system/configGroup.vue"),
    meta: { title: "设置分组" },
    hidden: false,
  },
  "/admin/system/configList": {
    name: "设置列表",
    component: () => import("@/views/admin/system/configList.vue"),
    meta: { title: "设置列表" },
    hidden: false,
  },
  "/admin/system/editor": {
    name: "富文本",
    component: () => import("@/views/admin/system/editor.vue"),
    meta: { title: "富文本" },
    hidden: false,
  },

  "/admin/account": {
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
