<template>
  <div class="dashboard-container">
    <div class="search-block">
      <el-form :inline="true" :model="searchForm">
        <el-form-item>
          <el-date-picker
            v-model="searchForm.time"
            type="datetimerange"
            value-format="X"
            format="YYYY-MM-DD HH:mm:ss"
            range-separator="至"
            start-placeholder="开始日期"
            end-placeholder="结束日期"
          />
        </el-form-item>
        <el-form-item>
          <el-input v-model="searchForm.name" placeholder="搜索名称" />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="onSearch">查询</el-button>
          <el-button @click="addVisible = true">新增</el-button>
          <el-button type="danger" @click="onDelete">删除</el-button>
        </el-form-item>
      </el-form>
    </div>

    <div class="data-list">
      <el-table
        v-loading="loading"
        :data="items"
        style="width: 100%"
        empty-text="暂无数据"
        @selection-change="handleSelectionChange"
      >
        <el-table-column type="selection" width="55" />
        <el-table-column prop="id" label="ID" width="80" />
        <el-table-column prop="type" label="角色标识" />
        <el-table-column prop="name" label="角色名称" />
        <el-table-column prop="remark" label="备注" />
        <el-table-column prop="create_time" label="创建时间" width="160" />
        <el-table-column label="状态" width="80" align="center">
          <template #default="scope">
            <el-tag v-if="scope.row.status === 1" disable-transitions>正常</el-tag>
            <el-tag v-if="scope.row.status === -1" disable-transitions type="info">禁用</el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="150" fixed="right">
          <template #default="scope">
            <el-button type="primary" size="small" @click="onItemEdit(scope.row)">编辑</el-button>
            <el-button type="warning" size="small" @click="onItemPerms(scope.row)">权限</el-button>
          </template>
        </el-table-column>
      </el-table>
    </div>
    <div class="page-block" style="text-align: center; margin: 10px 0">
      <el-pagination
        v-model:current-page="searchForm.page"
        v-model:page-size="searchForm.pagesize"
        :page-sizes="[20, 50, 100, 200, 300, 400]"
        layout="total, sizes, prev, pager, next, jumper"
        :total="total"
        @size-change="onSizeChange"
        @current-change="onCurrentChange"
      />
    </div>
    <el-dialog title="添加" v-model="addVisible">
      <el-form ref="addFormRef" :model="addForm">
        <el-form-item label="角色标识" label-width="80px">
          <el-input v-model="addForm.type" autocomplete="off" placeholder="请输入角色标识" />
        </el-form-item>
        <el-form-item label="角色名称" label-width="80px">
          <el-input v-model="addForm.name" autocomplete="off" placeholder="请输入角色名称" />
        </el-form-item>
        <el-form-item label="备注" label-width="80px">
          <el-input v-model="addForm.remark" autocomplete="off" placeholder="可空" />
        </el-form-item>
        <el-form-item label="状态" label-width="80px">
          <el-select v-model="addForm.status" placeholder="请选择">
            <el-option label="启用" :value="1" />
            <el-option label="停用" :value="-1" />
          </el-select>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="addVisible = false">取 消</el-button>
        <el-button type="primary" @click="onAdd" v-loading="loading">确 定</el-button>
      </template>
    </el-dialog>

    <el-dialog title="编辑" v-model="editVisible">
      <el-form ref="editFormRef" :model="editForm">
        <el-form-item label="角色标识" label-width="80px">
          <el-input v-model="editForm.type" autocomplete="off" placeholder="请输入角色标识" />
        </el-form-item>
        <el-form-item label="角色名称" label-width="80px">
          <el-input v-model="editForm.name" autocomplete="off" placeholder="请输入角色名称" />
        </el-form-item>
        <el-form-item label="备注" label-width="80px">
          <el-input v-model="editForm.remark" autocomplete="off" placeholder="可空" />
        </el-form-item>
        <el-form-item label="状态" label-width="80px">
          <el-select v-model="editForm.status" placeholder="请选择">
            <el-option label="启用" :value="1" />
            <el-option label="停用" :value="-1" />
          </el-select>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="editVisible = false">取 消</el-button>
        <el-button type="primary" @click="onEdit" v-loading="loading">确 定</el-button>
      </template>
    </el-dialog>

    <el-dialog title="权限" v-model="permsVisible">
      <el-form ref="permsFormRef" :model="permsForm">
        <el-tree
          ref="treeRef"
          :data="treeData"
          :props="treeProps"
          node-key="id"
          show-checkbox
          highlight-current
          :filter-node-method="filterNode"
          :expand-on-click-node="false"
        >
          <template #default="{ node, data }">
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
          </template>
        </el-tree>
      </el-form>
      <template #footer>
        <el-button @click="permsVisible = false">取 消</el-button>
        <el-button type="primary" @click="onPerms" v-loading="loading">确 定</el-button>
      </template>
    </el-dialog>
  </div>
</template>
<script setup>
import { ref, onMounted, nextTick } from "vue";
import { House } from "@element-plus/icons-vue";
import roles from "@/api/admin/roles";
import perms from "@/api/admin/perms";
import rolePerms from "@/api/admin/rolePerms";

const loading = ref(false);

const searchForm = ref({
  name: "",
  time: "",
  page: 1,
  pagesize: 20,
});
const total = ref(0);
const items = ref([]);

const addVisible = ref(false);
const addForm = ref({
  type: "",
  name: "",
  remark: "",
  status: 1,
});

const editVisible = ref(false);
const editForm = ref({
  id: "",
  type: "",
  name: "",
  remark: "",
  status: 1,
});
const multipleSelection = ref([]);

const onSearch = () => {
  searchForm.value.page = 1;
  getItems();
};
const getItems = async () => {
  loading.value = true;
  try {
    const response = await roles.getItems(searchForm.value);
    items.value = response.items;
    total.value = response.count;
  } catch (error) {
    console.error("获取列表失败:", error);
  } finally {
    loading.value = false;
  }
};

const onCurrentChange = (page) => {
  searchForm.value.page = page;
  getItems();
};
const onSizeChange = (size) => {
  searchForm.value.pagesize = size;
  getItems();
};

const onAdd = async () => {
  loading.value = true;
  try {
    await roles.add(addForm.value);
    addVisible.value = false;
    ElMessage.success("添加成功");
    getItems();
  } catch (error) {
    console.error("获取列表失败:", error);
  } finally {
    loading.value = false;
  }
};
const onItemEdit = (item) => {
  editForm.value = item;
  editVisible.value = true;
};
const onEdit = async () => {
  loading.value = true;
  try {
    await roles.edit(editForm.value);
    editVisible.value = false;
    ElMessage.success("编辑成功");
    getItems();
  } catch (error) {
    console.error("获取列表失败:", error);
  } finally {
    loading.value = false;
  }
};
const onDelete = () => {
  if (multipleSelection.value.length === 0) {
    ElMessage.warning("请先选择要删除的项");
    return;
  }
  ElMessageBox.confirm("此操作将永久删除选中数据, 是否继续?", "提示", {
    confirmButtonText: "确定",
    cancelButtonText: "取消",
    type: "warning",
  })
    .then(async () => {
      const loadingInstance = ElLoading.service({ fullscreen: true });
      try {
        const ids = multipleSelection.value.map((item) => item.id);
        await roles.del({ ids: ids });
        ElMessage.success("删除成功");
        getItems();
      } catch (error) {
      } finally {
        loadingInstance.close();
      }
    })
    .catch(() => {
      // catch error
    });
};

const handleSelectionChange = (val) => {
  multipleSelection.value = val;
};

//权限设置
const permsVisible = ref(false);
const permsFormRef = ref(null);
const permsForm = ref({
  type: "",
  perms_ids: [],
});
const treeData = ref([]);
const treeProps = {
  children: "children",
  label: "name",
  value: "id",
};
const treeRef = ref(null);
const filterNode = (value, data) => {
  if (!value) return true;
  return data.name.includes(value);
};
const onItemPerms = (item) => {
  permsForm.value.type = item.type;
  permsForm.value.perms_ids = item.perms_ids;
  permsVisible.value = true;
  nextTick(() => {
    if (treeRef.value) {
      console.log(treeRef.value);
      treeRef.value.setCheckedKeys([]);
      treeRef.value.setCheckedKeys(item.perms_ids);
    }
  });
};
const getTreeItems = async () => {
  const data = await perms.getItems();
  treeData.value = data;
};
const onPerms = async () => {
  const data = treeRef.value.getCheckedKeys();
  permsForm.value.perms_ids = data;

  loading.value = true;
  try {
    await rolePerms.save(permsForm.value);
    editVisible.value = false;
    ElMessage.success("编辑成功");
    permsVisible.value = false;
    getItems();
  } catch (error) {
  } finally {
    loading.value = false;
  }
};

onMounted(async () => {
  await getTreeItems();
  getItems();
});
</script>
