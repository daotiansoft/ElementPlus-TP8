<template>
  <div class="dashboard-container">
    <div class="balance-card">
      <span class="balance-label">我的余额</span>
      <span class="balance-value">￥{{ formatAmount(balance) }}</span>
    </div>

    <div class="search-block">
      <el-form :inline="true" :model="searchForm">
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
        </el-form-item>
      </el-form>
    </div>

    <div class="data-list">
      <el-table
        v-loading="loading"
        :data="items"
        style="width: 100%"
        empty-text="暂无数据"
      >
        <el-table-column prop="id" label="ID" width="80" />
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
        :page-sizes="[20, 50, 100, 200]"
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
import balanceApi from "@/api/user/balance";

const typeOptions = [
  { value: "recharge", label: "充值" },
  { value: "deduct", label: "扣减" },
  { value: "refund", label: "退款" },
  { value: "adjust", label: "调整" },
  { value: "consume", label: "消费" },
];

const loading = ref(false);
const balance = ref(0);
const searchForm = ref({
  type: "",
  time: "",
  page: 1,
  pagesize: 20,
});
const total = ref(0);
const items = ref([]);

// 分 -> 元
const formatAmount = (fen) => (Number(fen) / 100).toFixed(2);

const getBalance = async () => {
  try {
    const response = await balanceApi.getInfo();
    balance.value = response.balance;
  } catch (error) {
    console.error("获取余额失败:", error);
  }
};

const getItems = async () => {
  loading.value = true;
  try {
    const response = await balanceApi.getItems(searchForm.value);
    items.value = response.items;
    total.value = response.count;
  } catch (error) {
    console.error("获取列表失败:", error);
  } finally {
    loading.value = false;
  }
};

const onSearch = () => {
  searchForm.value.page = 1;
  getItems();
};

const onCurrentChange = (page) => {
  searchForm.value.page = page;
  getItems();
};

const onSizeChange = (size) => {
  searchForm.value.pagesize = size;
  getItems();
};

onMounted(() => {
  getBalance();
  getItems();
});
</script>

<style scoped>
.balance-card {
  display: flex;
  align-items: baseline;
  gap: 12px;
  padding: 16px 20px;
  margin-bottom: 16px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 8px;
  color: #fff;
}

.balance-label {
  font-size: 14px;
  opacity: 0.85;
}

.balance-value {
  font-size: 26px;
  font-weight: 600;
}

.amount-in {
  color: #67c23a;
}

.amount-out {
  color: #f56c6c;
}
</style>
