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
        <el-table-column prop="role_name" label="角色" width="80" />
        <el-table-column prop="username" label="账号" />
        <el-table-column label="余额(元)" width="120" align="right">
          <template #default="scope">
            {{ formatAmount(scope.row.balance) }}
          </template>
        </el-table-column>
        <el-table-column prop="create_time" label="创建时间" width="160" />
        <el-table-column label="状态" width="80" align="center">
          <template #default="scope">
            <el-tag v-if="scope.row.status === 1" disable-transitions>正常</el-tag>
            <el-tag v-if="scope.row.status === -1" disable-transitions type="info">禁用</el-tag>
          </template>
        </el-table-column>
        <el-table-column label="操作" width="180" fixed="right">
          <template #default="scope">
            <el-button type="primary" size="small" @click="onItemEdit(scope.row)">编辑</el-button>
            <el-button type="warning" size="small" @click="onItemAdjust(scope.row)"
              >调整余额</el-button
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
        <el-form-item label="角色" label-width="80px">
          <el-select v-model="addForm.type" placeholder="选择角色">
            <el-option v-for="(val, key) in roles" :key="key" :label="val.name" :value="val.type" />
          </el-select>
        </el-form-item>
        <el-form-item label="账号名称" label-width="80px" prop="username">
          <el-input v-model="addForm.username" autocomplete="off" placeholder="请输入账号名称" />
        </el-form-item>
        <el-form-item label="账号密码" label-width="80px" prop="password">
          <el-input
            v-model="addForm.password"
            autocomplete="off"
            placeholder="密码长度不可小于6个字符"
          />
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
      <el-form ref="edit_form" :model="editForm">
        <el-form-item label="角色" label-width="80px">
          <el-select v-model="editForm.type" placeholder="选择角色">
            <el-option v-for="(val, key) in roles" :key="key" :label="val.name" :value="val.type" />
          </el-select>
        </el-form-item>
        <el-form-item label="账号名称" label-width="80px" prop="username">
          <el-input v-model="editForm.username" autocomplete="off" placeholder="请输入账号名称" />
        </el-form-item>
        <el-form-item label="账号密码" label-width="80px" prop="password">
          <el-input
            v-model="editForm.password"
            autocomplete="off"
            placeholder="密码长度不可小于6个字符"
          />
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

    <el-dialog title="调整余额" v-model="adjustVisible">
      <el-form ref="adjustFormRef" :model="adjustForm" :rules="adjustRules">
        <el-form-item label="账号" label-width="80px">
          <el-input :model-value="adjustForm.username" disabled />
        </el-form-item>
        <el-form-item label="变动金额" label-width="80px" prop="amount">
          <el-input-number
            v-model="adjustForm.amount"
            :precision="2"
            :min="-1000000"
            :max="1000000"
            :step="1"
            style="width: 100%"
            placeholder="正数增加，负数扣减（单位：元）"
          />
        </el-form-item>
        <el-form-item label="交易类型" label-width="80px">
          <el-select v-model="adjustForm.type" placeholder="选择类型" style="width: 100%">
            <el-option v-for="t in typeOptions" :key="t.value" :label="t.label" :value="t.value" />
          </el-select>
        </el-form-item>
        <el-form-item label="备注" label-width="80px">
          <el-input v-model="adjustForm.remark" type="textarea" placeholder="备注" />
        </el-form-item>
      </el-form>
      <template #footer>
        <el-button @click="adjustVisible = false">取 消</el-button>
        <el-button type="primary" @click="onAdjust" v-loading="loading">确 定</el-button>
      </template>
    </el-dialog>
  </div>
</template>
<script setup>
import { ref, onMounted } from "vue";
import member from "@/api/admin/member";
import rolesApi from "@/api/admin/roles";

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
  type: "",
  username: "",
  password: "",
  status: 1,
});

const editVisible = ref(false);
const editForm = ref({
  id: "",
  type: "",
  username: "",
  password: "",
  status: 1,
});
const roles = ref([]);
const multipleSelection = ref([]);

const typeOptions = [
  { value: "recharge", label: "充值" },
  { value: "deduct", label: "扣减" },
  { value: "refund", label: "退款" },
  { value: "adjust", label: "调整" },
  { value: "consume", label: "消费" },
];

const adjustVisible = ref(false);
const adjustFormRef = ref(null);
const adjustForm = ref({
  user_id: null,
  username: "",
  amount: null,
  type: "adjust",
  remark: "",
});
const adjustRules = {
  amount: [{ required: true, message: "请输入变动金额", trigger: "blur" }],
};

// 分 -> 元
const formatAmount = (fen) => (Number(fen) / 100).toFixed(2);

const onSearch = () => {
  searchForm.value.page = 1;
  getItems();
};
const getItems = async () => {
  loading.value = true;
  try {
    const response = await member.getItems(searchForm.value);
    items.value = response.items;
    total.value = response.count;
  } catch (error) {
    console.error("获取列表失败:", error);
  } finally {
    loading.value = false;
  }
};
const getRoles = async () => {
  try {
    const response = await rolesApi.getOptions();
    roles.value = response;
  } catch (error) {
    console.error("获取列表失败:", error);
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
    await member.add(addForm.value);
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

const onItemAdjust = (item) => {
  adjustForm.value = {
    user_id: item.id,
    username: item.username,
    amount: null,
    type: "adjust",
    remark: "",
  };
  adjustVisible.value = true;
};

const onAdjust = async () => {
  const valid = await adjustFormRef.value.validate().catch(() => false);
  if (!valid) return;

  // 元 -> 分（整数）
  const changeAmount = Math.round(Number(adjustForm.value.amount) * 100);
  if (changeAmount === 0) {
    ElMessage.warning("变动金额不能为0");
    return;
  }

  loading.value = true;
  try {
    await member.adjust({
      user_id: adjustForm.value.user_id,
      change_amount: changeAmount,
      type: adjustForm.value.type,
      remark: adjustForm.value.remark,
    });
    ElMessage.success("调整成功");
    adjustVisible.value = false;
    getItems();
  } catch (error) {
    console.error("调整失败:", error);
  } finally {
    loading.value = false;
  }
};
const onEdit = async () => {
  loading.value = true;
  try {
    await member.edit(editForm.value);
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
        await member.del({ ids: ids });
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

onMounted(() => {
  getItems();
  getRoles();
});
</script>
