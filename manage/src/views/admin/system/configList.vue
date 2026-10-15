<template>
  <div class="dashboard-container">
    <div class="search-block">
      <el-form :inline="true" :model="searchForm">
        <el-form-item>
          <el-input v-model="searchForm.keyword" placeholder="搜索名称" />
        </el-form-item>
        <el-form-item>
          <el-input v-model="searchForm.key" placeholder="唯一标识" />
        </el-form-item>
        <el-form-item>
          <el-select v-model="searchForm.group_id" placeholder="筛选分组" style="width: 150px">
            <el-option
              v-for="val in groupOptions"
              :key="val.id"
              :label="val.name"
              :value="val.id"
            />
          </el-select>
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
        <el-table-column prop="group_name" label="分组" width="100" />
        <el-table-column prop="name" label="名称" width="120" />
        <el-table-column prop="key" label="唯一标识" width="180" />
        <el-table-column prop="type" label="类型" width="100" />
        <el-table-column prop="placeholder" label="描述" />
        <el-table-column prop="content" label="内容" />
        <el-table-column prop="params" label="附加参数" />
        <el-table-column prop="sort" label="排序" width="80" />
        <el-table-column label="是否显示" width="80" align="center">
          <template #default="scope">
            <el-tag v-if="scope.row.show === 1" disable-transitions>是</el-tag>
            <el-tag v-if="scope.row.show === -1" disable-transitions type="info">否</el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="80" fixed="right">
          <template #default="scope">
            <el-button type="primary" size="small" @click="onItemEdit(scope.row)">编辑</el-button>
          </template>
        </el-table-column>
      </el-table>
    </div>
    <div class="page-block" style="text-align: center; margin: 10px 0">
      <el-pagination
        :current-page="searchForm.page"
        :page-size="searchForm.pagesize"
        :page-sizes="[20, 50, 100, 200, 300, 400]"
        layout="total, sizes, prev, pager, next, jumper"
        :total="total"
        @size-change="onSizeChange"
        @current-change="onCurrentChange"
      />
    </div>
    <el-dialog title="添加" v-model="addVisible">
      <el-form ref="add_form" :model="addForm">
        <el-form-item label="分组" label-width="80px">
          <el-select v-model="addForm.group_id" placeholder="请选择">
            <el-option
              v-for="val in groupOptions"
              :key="val.id"
              :label="val.name"
              :value="val.id"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="名称" label-width="80px">
          <el-input v-model="addForm.name" autocomplete="off" placeholder="请输入名称" />
        </el-form-item>
        <el-form-item label="唯一标识" label-width="80px">
          <el-input v-model="addForm.key" autocomplete="off" placeholder="请输入唯一标识" />
        </el-form-item>
        <el-form-item label="类型" label-width="80px">
          <el-select v-model="addForm.type" placeholder="请选择">
            <el-option label="文本输入" value="text" />
            <el-option label="图片上传" value="image" />
            <el-option label="下拉选择" value="select" />
          </el-select>
        </el-form-item>
        <el-form-item label="描述内容" label-width="80px">
          <el-input v-model="addForm.placeholder" autocomplete="off" placeholder="可空，提示消息" />
        </el-form-item>
        <el-form-item label="附加参数" label-width="80px">
          <el-input v-model="addForm.params" autocomplete="off" placeholder="可空，长度小于255" />
        </el-form-item>
        <el-form-item label="配置内容" label-width="80px">
          <el-input
            v-model="addForm.content"
            autocomplete="off"
            placeholder="请输入配置内容，长度小于255"
          />
        </el-form-item>
        <el-form-item label="排序" label-width="80px">
          <el-input
            v-model="addForm.sort"
            autocomplete="off"
            placeholder="请输入排序,数字越大越靠前"
          />
        </el-form-item>
        <el-form-item label="是否显示" label-width="80px">
          <el-select v-model="addForm.show" placeholder="请选择">
            <el-option label="是" :value="1" />
            <el-option label="否" :value="-1" />
          </el-select>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="addVisible = false">取 消</el-button>
        <el-button type="primary" @click="onAdd" v-loading="loading">确 定</el-button>
      </template>
    </el-dialog>

    <el-dialog title="编辑" v-model="editVisible">
      <el-form ref="edit_form" :model="editForm">
        <el-form-item label="分组" label-width="80px">
          <el-select v-model="editForm.group_id" placeholder="请选择">
            <el-option
              v-for="val in groupOptions"
              :key="val.id"
              :label="val.name"
              :value="val.id"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="名称" label-width="80px">
          <el-input v-model="editForm.name" autocomplete="off" placeholder="请输入名称" />
        </el-form-item>
        <el-form-item label="唯一标识" label-width="80px">
          <el-input v-model="editForm.key" autocomplete="off" placeholder="请输入唯一标识" />
        </el-form-item>
        <el-form-item label="类型" label-width="80px">
          <el-select v-model="editForm.type" placeholder="请选择">
            <el-option label="文本输入" value="text" />
            <el-option label="图片上传" value="image" />
            <el-option label="下拉选择" value="select" />
          </el-select>
        </el-form-item>
        <el-form-item label="描述内容" label-width="80px">
          <el-input
            v-model="editForm.placeholder"
            autocomplete="off"
            placeholder="可空，提示消息"
          />
        </el-form-item>
        <el-form-item label="附加参数" label-width="80px">
          <el-input v-model="editForm.params" autocomplete="off" placeholder="可空，长度小于255" />
        </el-form-item>
        <el-form-item label="配置内容" label-width="80px">
          <el-input
            v-model="editForm.content"
            autocomplete="off"
            placeholder="请输入配置内容，长度小于255"
          />
        </el-form-item>
        <el-form-item label="排序" label-width="80px">
          <el-input
            v-model="editForm.sort"
            autocomplete="off"
            placeholder="请输入排序,数字越大越靠前"
          />
        </el-form-item>
        <el-form-item label="是否显示" label-width="80px">
          <el-select v-model="editForm.show" placeholder="请选择">
            <el-option label="是" :value="1" />
            <el-option label="否" :value="-1" />
          </el-select>
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="editVisible = false">取 消</el-button>
        <el-button type="primary" @click="onEdit" v-loading="loading">确 定</el-button>
      </template>
    </el-dialog>
  </div>
</template>
<script setup>
import { ref, onMounted } from "vue";
import configList from "@/api/admin/configList";
import configGroup from "@/api/admin/configGroup";

const loading = ref(false);

const searchForm = ref({
  keyword: "",
  time: "",
  page: 1,
  pagesize: 20,
});
const total = ref(0);
const items = ref([]);
const groupOptions = ref([]);

const addVisible = ref(false);
const addForm = ref({
  group_id: "",
  name: "",
  key: "",
  type: "",
  content: "",
  placeholder: "",
  params: "",
  sort: "",
  show: 1,
});

const editVisible = ref(false);
const editForm = ref({
  id: "",
  group_id: "",
  name: "",
  key: "",
  type: "",
  content: "",
  placeholder: "",
  params: "",
  sort: "",
  show: 1,
});
const multipleSelection = ref([]);

const onSearch = () => {
  searchForm.value.page = 1;
  getItems();
};
const getItems = async () => {
  loading.value = true;
  try {
    const response = await configList.getItems(searchForm.value);
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
    await configList.add(addForm.value);
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
  editForm.value = { ...item };
  editVisible.value = true;
};
const onEdit = async () => {
  loading.value = true;
  try {
    await configList.edit(editForm.value);
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
        await configList.del({ ids: ids });
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

const getGroupOptione = async () => {
  const response = await configGroup.getOptions();
  groupOptions.value = response;
};

onMounted(() => {
  getItems();
  getGroupOptione();
});
</script>
