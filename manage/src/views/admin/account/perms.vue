<template>
  <div class="perms-manage">
    <el-card class="tree-card">
      <el-tree
        ref="treeRef"
        :data="treeData"
        :props="treeProps"
        node-key="id"
        highlight-current
        :filter-node-method="filterNode"
        :expand-on-click-node="false"
      >
        <template #default="{ node, data }">
          <div class="menu">
            <div class="menu-left">
              <el-icon v-if="data.parent_id == 0">
                <House />
              </el-icon>
              {{ data.name }}
              <el-tag
                v-if="data.module != '' && data.controller != '' && data.action != ''"
                effect="plain"
                style="margin-left: 10px"
                size="small"
                type="primary"
              >
                {{ data.module }}/{{ data.controller }}/{{ data.action }}
              </el-tag>
              <el-tag type="info" size="small" v-if="data.only_api == 1">API</el-tag>
              <el-tag type="info" size="small" v-if="data.status != 1">禁用</el-tag>
            </div>
            <div class="menu-right">
              <el-button
                type="danger"
                size="small"
                v-if="data.module != '' && data.controller != '' && data.action == ''"
                @click="onCurd(data)"
                >CURD</el-button
              >
              <el-button
                type="primary"
                size="small"
                :icon="Plus"
                @click="onVisibleAdd(data)"
              ></el-button>
              <el-button
                type="primary"
                size="small"
                :icon="Edit"
                v-if="data.id > 0"
                @click="onVisibleEdit(data)"
              ></el-button>
              <el-button
                type="danger"
                size="small"
                :icon="Delete"
                v-if="data.id > 0"
                @click="onDelete(data)"
              ></el-button>
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
      @submit="onSubmit"
    />
  </div>
</template>

<script setup>
import { computed, ref, onMounted } from "vue";
import { House, Plus, Edit, Delete } from "@element-plus/icons-vue";
import perms from "@/api/admin/perms";
import FormDialog from "@/components/FormDialog/index.vue";

const dialogVisible = ref(false);
const mode = ref("add");
const dialogTitle = computed(() => (mode.value === "add" ? "添加节点" : "编辑节点"));
const loading = ref(false);

const formRules = {
  name: [{ required: true, message: "请输入节点名称", trigger: "blur" }],
};

const treeData = ref([]);

const treeProps = {
  children: "children",
  label: "name",
  value: "id",
};

const formData = ref({});

// 添加与编辑共用同一份字段配置；hidden 控制条件显示，disabledInEdit 控制编辑时不可修改
const formFields = computed(() => [
  {
    prop: "parent_id",
    label: "上级节点",
    type: "tree-select",
    span: 12,
    placeholder: "请选择上级节点（留空为根节点）",
    props: {
      data: treeData.value,
      props: treeProps,
      checkStrictly: true,
      clearable: true,
      style: "width: 100%",
    },
  },
  { prop: "name", label: "节点名称", span: 12, placeholder: "请输入节点名称" },
  { prop: "module", label: "所属模块", placeholder: "请输入所属模块" },
  { prop: "controller", label: "控制器", placeholder: "请输入控制器" },
  { prop: "action", label: "操作方法", placeholder: "请输入操作方法" },
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
  {
    prop: "only_api",
    label: "仅API",
    type: "switch",
    span: 12,
    props: { activeValue: 1, inactiveValue: 0, activeText: "是", inactiveText: "否" },
  },
  {
    prop: "route_status",
    label: "前端路由",
    type: "switch",
    span: 12,
    props: { activeValue: 1, inactiveValue: 0, activeText: "是", inactiveText: "否" },
    hidden: (mode, model) => model.only_api != 0,
  },
  {
    prop: "route_name",
    label: "路由名称",
    span: 12,
    placeholder: "",
    hidden: (mode, model) => model.only_api != 0 || model.route_status != 1,
  },
]);

const onVisibleAdd = (item) => {
  mode.value = "add";
  formData.value = {
    id: 0,
    parent_id: item.id,
    name: "",
    module: item.module,
    controller: item.controller,
    action: item.action,
    sort: 0,
    status: 1,
    route_status: 0,
    route_name: "",
    only_api: 0,
  };
  dialogVisible.value = true;
};
const onVisibleEdit = (item) => {
  mode.value = "edit";
  formData.value = {
    id: item.id,
    parent_id: item.parent_id,
    name: item.name,
    module: item.module,
    controller: item.controller,
    action: item.action,
    sort: item.sort,
    status: item.status,
    route_status: item.route_status,
    route_name: item.route_name,
    only_api: item.only_api,
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
    getItems();
  } catch (error) {
  } finally {
    loading.value = false;
  }
};
const treeRef = ref(null);

// 过滤节点
const filterNode = (value, data) => {
  if (!value) return true;
  return data.name.includes(value);
};

// 删除节点
const onDelete = (data) => {
  ElMessageBox.confirm(`确定要删除节点 "${data.name}" 及其所有子节点吗？`, "删除确认", {
    confirmButtonText: "确定",
    cancelButtonText: "取消",
    type: "warning",
  })
    .then(async () => {
      loading.value = true;
      try {
        await perms.del({ ids: [data.id] });
        ElMessage.success("删除成功");
        getItems();
      } catch (error) {
      } finally {
        loading.value = false;
      }
    })
    .catch(() => {});
};
const onCurd = (data) => {
  ElMessageBox.confirm(`生成 "${data.name}" CURD？`, "！！！非开发人员，请勿操作！！！", {
    confirmButtonText: "确定",
    cancelButtonText: "取消",
    type: "warning",
  })
    .then(async () => {
      loading.value = true;
      try {
        await perms.createCurd({ id: data.id });
        ElMessage.success("操作成功，当前仅生成后端curd");
        getItems();
      } catch (error) {
      } finally {
        loading.value = false;
      }
    })
    .catch(() => {});
};

const getItems = () => {
  const rootTree = [
    {
      id: 0,
      parent_id: 0,
      name: "根节点",
      module: "",
      controller: "",
      action: "",
      status: 1,
    },
  ];
  perms.getItems().then((data) => {
    treeData.value = [...rootTree, ...data];
  });
};

onMounted(() => {
  getItems();
});
</script>

<style scoped lang="scss">
.menu {
  display: flex;
  width: 100%;

  &-left {
    flex: 1;
  }
}
</style>
