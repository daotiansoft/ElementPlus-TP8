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
          <el-input v-model="searchForm.username" placeholder="搜索账号" />
        </el-form-item>
        <el-form-item>
          <el-input v-model="searchForm.appid" placeholder="APPID" />
        </el-form-item>
        <el-form-item>
          <el-button type="primary" @click="onSearch">查询</el-button>
          <el-button @click="onAdd">新增</el-button>
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
        <el-table-column label="接口参数">
          <template #default="scope">
            <div>
              AppID:<span style="margin-left: 5px">{{ scope.row.appid }}</span>
            </div>
            <div>
              Secret:<span style="margin-left: 5px">{{ scope.row.secret }}</span>
            </div>
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="创建时间" width="160" />
        <el-table-column label="状态" width="80" align="center">
          <template #default="scope">
            <el-tag v-if="scope.row.status === 1" disable-transitions>正常</el-tag>
            <el-tag v-if="scope.row.status === -1" disable-transitions type="info">禁用</el-tag>
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
    <FormDialog
      v-model="dialogVisible"
      :title="dialogTitle"
      :mode="mode"
      v-model:model="form"
      :fields="formFields"
      :loading="loading"
      @submit="onSubmit"
    >
      <template #appid="{ disabled }">
        <el-input v-model="form.appid" :disabled="disabled" placeholder="32个字符以内">
          <template #append>
            <el-button :disabled="disabled" @click="form.appid = generateRandomString()">
              随机生成
            </el-button>
          </template>
        </el-input>
      </template>
      <template #secret="{ disabled }">
        <el-input v-model="form.secret" :disabled="disabled" placeholder="32个字符以内">
          <template #append>
            <el-button :disabled="disabled" @click="form.secret = generateRandomString()">
              随机生成
            </el-button>
          </template>
        </el-input>
      </template>
    </FormDialog>
  </div>
</template>
<script setup>
import { computed, ref, onMounted } from "vue";
import appList from "@/api/admin/appList";
import FormDialog from "@/components/FormDialog/index.vue";

const loading = ref(false);

const searchForm = ref({
  keyword: "",
  time: "",
  page: 1,
  pagesize: 20,
});
const total = ref(0);
const items = ref([]);

const dialogVisible = ref(false);
const mode = ref("add");
const form = ref({});
const dialogTitle = computed(() => (mode.value === "add" ? "添加" : "编辑"));

// 添加与编辑共用同一份字段配置；disabledInEdit 的字段在编辑时不可修改
const formFields = [
  { prop: "username", label: "账号", placeholder: "请输入账号" },
  { prop: "appid", label: "APPID", slot: "appid", disabledInEdit: true },
  { prop: "secret", label: "SECRET", slot: "secret" },
  {
    prop: "status",
    label: "状态",
    type: "select",
    placeholder: "请选择",
    options: [
      { label: "启用", value: 1 },
      { label: "停用", value: -1 },
    ],
  },
];

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

const onAdd = () => {
  mode.value = "add";
  form.value = { username: "", appid: "", secret: "", status: 1 };
  dialogVisible.value = true;
};
const onItemEdit = (item) => {
  mode.value = "edit";
  form.value = { ...item };
  dialogVisible.value = true;
};
const onSubmit = async () => {
  loading.value = true;
  try {
    if (mode.value === "add") {
      await appList.add(form.value);
      ElMessage.success("添加成功");
    } else {
      await appList.edit(form.value);
      ElMessage.success("编辑成功");
    }
    dialogVisible.value = false;
    getItems();
  } catch (error) {
    console.error("保存失败:", error);
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
        await appList.del({ ids: ids });
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
  return Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
};

onMounted(() => {
  getItems();
});
</script>
