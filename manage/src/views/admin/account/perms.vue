<template>
  <div class="perms-manage">
    <el-card class="tree-card" shadow="never">
      <div class="page-heading">
        <div>
          <h2>权限节点</h2>
          <p>管理菜单层级、页面路由与接口操作权限</p>
        </div>
        <div class="node-legend" aria-label="节点类型说明">
          <el-tag
            v-for="option in nodeTypeOptions"
            :key="option.value"
            size="small"
            effect="light"
            :type="nodeTypeTag(option.value)"
            >{{ option.label }}</el-tag
          >
        </div>
      </div>
      <div class="toolbar">
        <el-input
          v-model="filterText"
          clearable
          placeholder="搜索名称、模块、控制器、方法或路由"
          :prefix-icon="Search"
          class="filter-input"
          aria-label="搜索权限节点"
        />
        <el-button type="primary" :icon="Plus" @click="onVisibleAdd(rootNode)"
          >新增根节点</el-button
        >
      </div>
      <el-tree
        ref="treeRef"
        v-loading="loading"
        :data="treeData"
        :props="treeProps"
        node-key="id"
        highlight-current
        :filter-node-method="filterNode"
        :expand-on-click-node="false"
        empty-text="暂无匹配的权限节点"
        default-expand-all
      >
        <template #default="{ data }">
          <div class="menu" :class="{ 'is-disabled': data.status !== 1 }">
            <div class="menu-left">
              <span class="node-icon" :class="`node-icon--${data.node_type}`">
                <el-icon
                  ><component
                    :is="
                      { group: Folder, page: Document, action: Key, api: Connection }[
                        data.node_type
                      ] || Folder
                    "
                /></el-icon>
              </span>
              <div class="node-details">
                <div class="node-title">
                  <span class="node-name">{{ data.name }}</span>
                  <el-tag size="small" effect="plain" :type="nodeTypeTag(data.node_type)">
                    {{ nodeTypeLabel(data.node_type) }}
                  </el-tag>
                  <el-tag v-if="data.status !== 1" size="small" type="info">禁用</el-tag>
                </div>
                <div v-if="data.route_path || (data.module && data.controller)" class="node-meta">
                  <el-tag v-if="data.route_path" size="small" type="success" effect="plain">
                    {{ data.route_path }}
                  </el-tag>
                  <el-tag
                    v-if="data.module && data.controller && data.action"
                    size="small"
                    type="warning"
                    effect="plain"
                  >
                    {{ data.module }}/{{ data.controller }}/{{ data.action }}
                  </el-tag>
                  <el-tag
                    v-else-if="data.module && data.controller"
                    size="small"
                    type="warning"
                    effect="plain"
                  >
                    CRUD: {{ data.module }}/{{ data.controller }}
                  </el-tag>
                </div>
              </div>
            </div>
            <div class="menu-right">
              <el-button
                v-if="data.node_type === 'page' && data.module && data.controller"
                type="warning"
                size="small"
                plain
                @click.stop="onCurd(data)"
              >
                生成操作权限
              </el-button>
              <el-button
                type="primary"
                size="small"
                :icon="Plus"
                aria-label="添加子节点"
                title="添加子节点"
                plain
                @click.stop="onVisibleAdd(data)"
              />
              <el-button
                v-if="data.id > 0"
                type="primary"
                size="small"
                :icon="Edit"
                aria-label="编辑节点"
                title="编辑节点"
                plain
                @click.stop="onVisibleEdit(data)"
              />
              <el-button
                v-if="data.id > 0"
                type="danger"
                size="small"
                :icon="Delete"
                aria-label="删除节点"
                title="删除节点"
                plain
                @click.stop="onDelete(data)"
              />
            </div>
          </div>
        </template>
      </el-tree>
    </el-card>

    <FormDialog
      v-model="dialogVisible"
      :title="dialogTitle"
      :mode="mode"
      :model="formData"
      :fields="formFields"
      :rules="formRules"
      label-width="auto"
      :gutter="24"
      :loading="loading"
      destroy-on-close
      width="720px"
      @submit="onSubmit"
    />
  </div>
</template>

<script setup>
import { computed, ref, onMounted, watch } from "vue";
import {
  Folder,
  Document,
  Key,
  Connection,
  Plus,
  Edit,
  Delete,
  Search,
} from "@element-plus/icons-vue";
import perms from "@/api/admin/perms";
import FormDialog from "@/components/FormDialog/index.vue";
import { routeOptions } from "@/router/routes";

const dialogVisible = ref(false);
const mode = ref("add");
const loading = ref(false);
const filterText = ref("");
const treeRef = ref(null);
const treeData = ref([]);
const formData = ref({});
const dialogTitle = computed(() => (mode.value === "add" ? "添加权限节点" : "编辑权限节点"));

const rootNode = computed(() => ({
  id: 0,
  parent_id: 0,
  name: "根节点",
  node_type: "group",
  module: "",
  controller: "",
  action: "",
  status: 1,
  children: treeData.value,
}));

const formRules = {
  name: [{ required: true, message: "请输入节点名称", trigger: "blur" }],
  node_type: [{ required: true, message: "请选择节点类型", trigger: "change" }],
};

const treeProps = {
  children: "children",
  label: "name",
  value: "id",
};

const nodeTypeOptions = [
  { label: "目录", value: "group" },
  { label: "页面", value: "page" },
  { label: "操作权限", value: "action" },
  { label: "API 权限", value: "api" },
];

const collectDescendants = (node, ids = new Set()) => {
  ids.add(node.id);
  (node.children || []).forEach((child) => collectDescendants(child, ids));
  return ids;
};

const filterParentOptions = (nodes, excludedIds) =>
  nodes
    .filter((node) => !excludedIds.has(node.id) && ["group", "page"].includes(node.node_type))
    .map((node) => ({
      ...node,
      children: filterParentOptions(node.children || [], excludedIds),
    }));

const getParentOptions = () => {
  const excludedIds =
    mode.value === "edit" && formData.value.id > 0
      ? collectDescendants(
          findNode(treeData.value, formData.value.id) || { id: formData.value.id, children: [] }
        )
      : new Set();
  return filterParentOptions([rootNode.value], excludedIds);
};

const findNode = (nodes, id) => {
  for (const node of nodes) {
    if (node.id === id) return node;
    const found = findNode(node.children || [], id);
    if (found) return found;
  }
  return null;
};

const formFields = computed(() => [
  {
    prop: "parent_id",
    label: "上级节点",
    type: "tree-select",
    span: 12,
    placeholder: "请选择上级节点",
    props: {
      data: getParentOptions(),
      props: treeProps,
      checkStrictly: true,
      clearable: false,
      style: "width: 100%",
    },
  },
  {
    prop: "node_type",
    label: "节点类型",
    type: "select",
    span: 12,
    options: nodeTypeOptions,
    placeholder: "请选择节点类型",
  },
  { prop: "name", label: "节点名称", span: 12, placeholder: "请输入节点名称" },
  {
    prop: "route_path",
    label: "绑定前端路由",
    type: "select",
    span: 24,
    placeholder: "请选择已登记的路由",
    options: (_mode, model) =>
      routeOptions.filter((option) => option.node_type === model.node_type),
    props: { clearable: true, filterable: true, style: "width: 100%" },
    hidden: (_mode, model) => !["group", "page"].includes(model.node_type),
  },
  {
    prop: "module",
    label: "CURD 模块",
    span: 12,
    placeholder: "可选，例如 admin",
    hidden: (_mode, model) => model.node_type !== "page",
  },
  {
    prop: "controller",
    label: "CURD 控制器",
    span: 12,
    placeholder: "可选，例如 Member",
    hidden: (_mode, model) => model.node_type !== "page",
  },
  {
    prop: "module",
    label: "所属模块",
    span: 12,
    placeholder: "例如 admin",
    hidden: (_mode, model) => !["action", "api"].includes(model.node_type),
  },
  {
    prop: "controller",
    label: "控制器",
    span: 12,
    placeholder: "例如 Member",
    hidden: (_mode, model) => !["action", "api"].includes(model.node_type),
  },
  {
    prop: "action",
    label: "操作方法",
    span: 12,
    placeholder: "例如 items",
    hidden: (_mode, model) => !["action", "api"].includes(model.node_type),
  },
  {
    prop: "sort",
    label: "排序",
    type: "number",
    span: 12,
    props: { min: 0, max: 9999, controlsPosition: "right" },
  },
  {
    prop: "status",
    label: "状态",
    type: "switch",
    span: 12,
    props: { activeValue: 1, inactiveValue: 0, activeText: "启用", inactiveText: "禁用" },
  },
]);

const nodeTypeLabel = (type) =>
  nodeTypeOptions.find((option) => option.value === type)?.label || "未知类型";
const nodeTypeTag = (type) =>
  ({ group: "info", page: "success", action: "warning", api: "primary" })[type] || "info";

const onVisibleAdd = (item) => {
  mode.value = "add";
  const defaultType = item.node_type === "page" ? "action" : "group";
  formData.value = {
    id: 0,
    parent_id: item.id,
    name: "",
    node_type: defaultType,
    route_path: "",
    module: ["action", "api"].includes(defaultType) ? item.module || "" : "",
    controller: ["action", "api"].includes(defaultType) ? item.controller || "" : "",
    action: "",
    sort: 0,
    status: 1,
  };
  dialogVisible.value = true;
};

const onVisibleEdit = (item) => {
  mode.value = "edit";
  formData.value = {
    id: item.id,
    parent_id: item.parent_id,
    name: item.name,
    node_type: item.node_type,
    route_path: item.route_path || "",
    module: item.module || "",
    controller: item.controller || "",
    action: item.action || "",
    sort: item.sort,
    status: item.status,
  };
  dialogVisible.value = true;
};

const onSubmit = async (data) => {
  loading.value = true;
  try {
    if (mode.value === "add") {
      await perms.add(data);
      ElMessage.success("添加成功");
    } else {
      await perms.edit(data);
      ElMessage.success("编辑成功");
    }
    dialogVisible.value = false;
    await getItems();
  } catch (error) {
    console.error("保存权限节点失败:", error);
  } finally {
    loading.value = false;
  }
};

const filterNode = (value, data) => {
  if (!value) return true;
  const keyword = value.toLowerCase();
  return [data.name, data.route_path, data.module, data.controller, data.action]
    .filter(Boolean)
    .some((field) => String(field).toLowerCase().includes(keyword));
};

watch(filterText, (value) => treeRef.value?.filter(value));

const onDelete = (data) => {
  ElMessageBox.confirm(`确定删除“${data.name}”及其所有子节点吗？`, "删除确认", {
    confirmButtonText: "确定",
    cancelButtonText: "取消",
    type: "warning",
  })
    .then(async () => {
      loading.value = true;
      try {
        await perms.del({ ids: [data.id] });
        ElMessage.success("删除成功");
        await getItems();
      } catch (error) {
        console.error("删除权限节点失败:", error);
      } finally {
        loading.value = false;
      }
    })
    .catch(() => {});
};

const onCurd = (data) => {
  ElMessageBox.confirm(`为“${data.name}”生成 CRUD 操作权限？`, "生成确认", {
    confirmButtonText: "确定",
    cancelButtonText: "取消",
    type: "warning",
  })
    .then(async () => {
      loading.value = true;
      try {
        await perms.createCurd({ id: data.id });
        ElMessage.success("操作权限已生成");
        await getItems();
      } catch (error) {
        console.error("生成操作权限失败:", error);
      } finally {
        loading.value = false;
      }
    })
    .catch(() => {});
};

const getItems = async () => {
  loading.value = true;
  try {
    treeData.value = await perms.getItems();
  } catch (error) {
    console.error("获取权限节点失败:", error);
  } finally {
    loading.value = false;
  }
};

onMounted(getItems);
</script>

<style scoped lang="scss">
.tree-card {
  :deep(.el-card__body) {
    padding: 12px 16px;
  }
  :deep(.el-tree) {
    --el-tree-node-hover-bg-color: var(--el-fill-color-light);
  }
  :deep(.el-tree-node__content) {
    height: auto;
    min-height: 36px;
    padding-block: 4px;
    padding-right: 12px;
    border-radius: 8px;
  }
  :deep(.el-tree-node.is-current > .el-tree-node__content) {
    background: var(--el-color-primary-light-9);
  }
}
.page-heading {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  margin-bottom: 14px;
  h2 {
    font-size: 17px;
    line-height: 1.5;
    font-weight: 600;
  }
  p {
    margin-top: 3px;
    color: var(--el-text-color-secondary);
    font-size: 13px;
    line-height: 1.6;
  }
}
.node-legend {
  display: flex;
  flex-wrap: wrap;
  gap: 6px;
}
.toolbar {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  justify-content: space-between;
  padding: 10px 12px;
  margin-bottom: 8px;
  background: var(--el-fill-color-light);
  border: 1px solid var(--el-border-color-lighter);
  border-radius: 10px;
}
.filter-input {
  width: 440px;
  max-width: 100%;
}
.menu {
  display: flex;
  width: 100%;
  min-width: 0;
  gap: 12px;
  align-items: center;
  &-left {
    display: flex;
    flex: 1;
    min-width: 0;
    align-items: center;
    gap: 8px;
  }
  &-right {
    display: flex;
    flex-shrink: 0;
    gap: 6px;
    align-items: center;
    .el-button {
      height: 26px;
      min-height: 26px;
      margin: 0;
      padding: 0 7px;
      font-size: 12px;
    }
  }
  &.is-disabled .node-name {
    color: var(--el-text-color-secondary);
  }
}
.node-icon {
  display: flex;
  flex-shrink: 0;
  align-items: center;
  justify-content: center;
  width: 26px;
  height: 26px;
  border-radius: 8px;
  font-size: 14px;
  color: var(--el-color-info);
  background: var(--el-color-info-light-9);
  &--page {
    color: var(--el-color-success);
    background: var(--el-color-success-light-9);
  }
  &--action {
    color: var(--el-color-warning);
    background: var(--el-color-warning-light-9);
  }
  &--api {
    color: var(--el-color-primary);
    background: var(--el-color-primary-light-9);
  }
}
.node-details {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 3px 10px;
  min-width: 0;
}
.node-title,
.node-meta {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 3px 6px;
  :deep(.el-tag) {
    max-width: 100%;
    height: auto;
    min-height: 20px;
    white-space: normal;
  }
  :deep(.el-tag__content) {
    min-width: 0;
    overflow-wrap: anywhere;
    white-space: normal;
  }
}
.node-name {
  font-size: 13px;
  font-weight: 500;
  line-height: 1.6;
  overflow-wrap: anywhere;
}
.node-meta {
  margin-top: 0;
  font-family: ui-monospace, SFMono-Regular, Menlo, monospace;
  :deep(.el-tag) {
    color: var(--el-text-color-regular);
    background: var(--el-fill-color-light);
    border-color: var(--el-border-color-light);
    font-weight: 400;
  }
}
@media (max-width: 767px) {
  .tree-card :deep(.el-card__body) {
    padding: 16px 10px;
  }
  .page-heading {
    align-items: flex-start;
    flex-direction: column;
    margin-inline: 6px;
    margin-bottom: 12px;
    gap: 12px;
  }
  .toolbar {
    padding: 12px;
  }
  .filter-input {
    width: 100%;
  }
  .menu {
    flex-direction: column;
    align-items: flex-start;
    gap: 8px;
  }
  .menu-left {
    width: 100%;
  }
  .menu-right {
    flex-wrap: wrap;
    padding-left: 34px;
  }
}
</style>
