<template>
  <div class="dashboard-container">
    <div class="search-block">
      <el-form :inline="true" :model="searchForm">
        <el-form-item>
          <el-select
            v-model="searchForm.user_id"
            placeholder="选择用户"
            clearable
            filterable
            style="width: 160px"
          >
            <el-option
              v-for="item in userOptions"
              :key="item.id"
              :label="item.username"
              :value="item.id"
            />
          </el-select>
        </el-form-item>
        <el-form-item>
          <el-select v-model="searchForm.type" placeholder="交易类型" clearable style="width: 140px">
            <el-option
              v-for="t in typeOptions"
              :key="t.value"
              :label="t.label"
              :value="t.value"
            />
          </el-select>
        </el-form-item>
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
          <el-button type="primary" @click="onSearch">查询</el-button>
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
        <el-table-column prop="username" label="用户" width="120" />
        <el-table-column label="变动金额(元)" width="140" align="right">
          <template #default="scope">
            <span :class="scope.row.change_amount > 0 ? 'amount-in' : 'amount-out'">
              {{ formatAmount(scope.row.change_amount) }}
            </span>
          </template>
        </el-table-column>
        <el-table-column label="变动后余额(元)" width="140" align="right">
          <template #default="scope">
            {{ formatAmount(scope.row.balance_after) }}
          </template>
        </el-table-column>
        <el-table-column prop="type_name" label="类型" width="100" />
        <el-table-column prop="remark" label="备注" />
        <el-table-column prop="operator" label="操作员" width="120" />
        <el-table-column prop="create_time" label="时间" width="160" />
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
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import balance from "@/api/admin/balance";
import member from "@/api/admin/member";

const typeOptions = [
  { value: "recharge", label: "充值" },
  { value: "deduct", label: "扣减" },
  { value: "refund", label: "退款" },
  { value: "adjust", label: "调整" },
  { value: "consume", label: "消费" },
];

const loading = ref(false);
const searchForm = ref({
  user_id: "",
  type: "",
  time: "",
  page: 1,
  pagesize: 20,
});
const total = ref(0);
const items = ref([]);
const userOptions = ref([]);
const multipleSelection = ref([]);

// 分 -> 元
const formatAmount = (fen) => (Number(fen) / 100).toFixed(2);

const onSearch = () => {
  searchForm.value.page = 1;
  getItems();
};

const getItems = async () => {
  loading.value = true;
  try {
    const response = await balance.getItems(searchForm.value);
    items.value = response.items;
    total.value = response.count;
  } catch (error) {
    console.error("获取列表失败:", error);
  } finally {
    loading.value = false;
  }
};

const getUsers = async () => {
  try {
    userOptions.value = await member.getOptions();
  } catch (error) {
    console.error("获取用户失败:", error);
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

const onDelete = () => {
  if (multipleSelection.value.length === 0) {
    ElMessage.warning("请先选择要删除的记录");
    return;
  }
  ElMessageBox.confirm("此操作将永久删除选中记录, 是否继续?", "提示", {
    confirmButtonText: "确定",
    cancelButtonText: "取消",
    type: "warning",
  })
    .then(async () => {
      const loadingInstance = ElLoading.service({ fullscreen: true });
      try {
        const ids = multipleSelection.value.map((item) => item.id);
        await balance.del({ ids });
        ElMessage.success("删除成功");
        getItems();
      } catch (error) {
        console.error("删除失败:", error);
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
  getUsers();
});
</script>

<style scoped>
.amount-in {
  color: #67c23a;
}
.amount-out {
  color: #f56c6c;
}
</style>
