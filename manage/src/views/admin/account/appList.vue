<template>
  <div class="dashboard-container">
    <div class="search-block">
      <el-form :inline="true" :model="searchForm">
        <el-form-item>
          <el-date-picker
            v-model="searchForm.time"
            type="datetimerange"
            value-format="yyyy-MM-dd HH:mm:ss"
            format="yyyy-MM-dd HH:mm:ss"
            range-separator="至"
            start-placeholder="开始日期"
            end-placeholder="结束日期"
          />
        </el-form-item>
        <el-form-item>
          <el-input v-model="searchForm.username" placeholder="搜索账号" />
        </el-form-item>
        <el-form-item>
          <el-input v-model="searchForm.appid" placeholder="APPID" />
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
        <el-table-column prop="username" label="账号" />
        <el-table-column prop="appid" label="APPID" />
        <el-table-column prop="secret" label="SECRET" />
        <el-table-column prop="create_time" label="创建时间" width="160" />
        <el-table-column label="状态" width="80" align="center">
          <template #default="scope">
            <el-tag v-if="scope.row.status === 1" disable-transitions
              >正常</el-tag
            >
            <el-tag
              v-if="scope.row.status === -1"
              disable-transitions
              type="info"
              >禁用</el-tag
            >
          </template>
        </el-table-column>
        <el-table-column label="操作" width="80" fixed="right">
          <template #default="scope">
            <el-button
              type="primary"
              size="small"
              @click="onItemEdit(scope.row)"
              >编辑</el-button
            >
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
      <el-form ref="add_form" :model="addForm">
        <el-form-item label="账号" label-width="80px">
          <el-input
            v-model="addForm.username"
            autocomplete="off"
            placeholder="请输入账号"
          />
        </el-form-item>
        <el-form-item label="APPID" label-width="80px">
          <el-input placeholder="32个字符以内" v-model="addForm.appid">
            <template #append>
              <el-button
                @click="
                  () => {
                    addForm.appid = generateRandomString();
                  }
                "
                >随机生成</el-button
              >
            </template>
          </el-input>
        </el-form-item>
        <el-form-item label="SECRET" label-width="80px">
          <el-input placeholder="32个字符以内" v-model="addForm.secret">
            <template #append>
              <el-button
                slot="append"
                @click="
                  () => {
                    addForm.secret = generateRandomString();
                  }
                "
                >随机生成</el-button
              >
            </template>
          </el-input>
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
        <el-button type="primary" @click="onAdd" v-loading="loading"
          >确 定</el-button
        >
      </template>
    </el-dialog>

    <el-dialog title="编辑" v-model="editVisible">
      <el-form ref="edit_form" :model="editForm">
        <el-form-item label="账号" label-width="80px">
          <el-input
            v-model="editForm.username"
            autocomplete="off"
            placeholder="请输入账号"
          />
        </el-form-item>
        <el-form-item label="APPID" label-width="80px">
          <el-input placeholder="32个字符以内" v-model="editForm.appid">
            <template #append>
              <el-button
                slot="append"
                @click="
                  () => {
                    editForm.appid = generateRandomString();
                  }
                "
                >随机生成</el-button
              >
            </template>
          </el-input>
        </el-form-item>
        <el-form-item label="SECRET" label-width="80px">
          <el-input placeholder="32个字符以内" v-model="editForm.secret">
            <template #append>
              <el-button
                slot="append"
                @click="
                  () => {
                    editForm.secret = generateRandomString();
                  }
                "
                >随机生成</el-button
              >
            </template>
          </el-input>
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
        <el-button type="primary" @click="onEdit" v-loading="loading"
          >确 定</el-button
        >
      </template>
    </el-dialog>
  </div>
</template>
<script setup>
import { ref, onMounted } from "vue";
import appList from "@/api/admin/appList";

const loading = ref(false);

const searchForm = ref({
  keyword: "",
  time: "",
  page: 1,
  pagesize: 20,
});
const total = ref(0);
const items = ref([]);

const addVisible = ref(false);
const addForm = ref({
  username: "",
  appid: "",
  secret: "",
  status: 1,
});

const editVisible = ref(false);
const editForm = ref({
  id: "",
  username: "",
  appid: "",
  secret: "",
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
    const response = await appList.getItems(searchForm.value);
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
    await appList.add(addForm.value);
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
    await appList.edit(editForm.value);
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
        appList.del({ ids: ids });
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

const generateRandomString = (length = 32) => {
  return (
    Math.random().toString(36).substring(2, 15) +
    Math.random().toString(36).substring(2, 15)
  );
};

onMounted(() => {
  getItems();
});
</script>
