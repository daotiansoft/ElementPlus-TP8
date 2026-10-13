<template>
  <div class="dashboard">
    <el-form
      ref="formRef"
      label-width="100px"
      style="max-width: 1000px"
      @keyup.enter="submitForm(formRef)"
    >
      <el-tabs
        v-model="activeName"
        type="card"
        style="background: #fff; padding: 20px; border-radius: 4px"
      >
        <el-tab-pane
          v-for="groupItem in items"
          :key="groupItem.id"
          :label="groupItem.name"
          :name="groupItem.id"
        >
          <el-form-item
            v-for="item in groupItem['child']"
            :key="item.key"
            :label="item.name"
          >
            <el-input v-if="item.type == 'text'" v-model="item.content" />
            <el-input
              v-if="item.type == 'textarea'"
              v-model="item.content"
              type="textarea"
            />
            <el-input v-if="item.type == 'image'" v-model="item.content">
              <template #append>
                <InputUpload
                  @input="
                    (val) => {
                      setImage(val, item.key);
                    }
                  "
                />
              </template>
            </el-input>
            <el-select v-if="item.type == 'select'" v-model="item.content">
              <el-option
                v-for="option in item.params"
                :key="option.id"
                :label="option.name"
                :value="option.id"
              />
            </el-select>
            <div style="font-size: 12px; color: #909399">
              {{ item.placeholder }}
            </div>
          </el-form-item>
        </el-tab-pane>
      </el-tabs>
      <div
        style="
          display: flex;
          justify-content: flex-end;
          background-color: #fff;
          padding: 10px;
        "
      >
        <el-button type="primary" @click="submitForm(formRef)">保存</el-button>
      </div>
    </el-form>
  </div>
</template>
<script setup>
import InputUpload from "@/components/Upload/InputUpload.vue";
import { ref, onMounted } from "vue";

import config from "@/api/admin/config";
const items = ref([]);
const formRef = ref();
const activeName = ref("");

onMounted(() => {
  getItems();
});
const getItems = async () => {
  try {
    const data = await config.getItems();
    items.value = data;
    activeName.value = data[0].id ?? "";
  } catch (error) {
    console.error("获取配置失败:", error);
  }
};
const submitForm = async (formEl) => {
  if (!formEl) return;
  try {
    await config.postItems(items.value);
    ElMessage.success("保存配置成功");
  } catch (error) {
    console.error("保存配置失败:", error);
    ElMessage.error("保存配置失败");
  }
};
const setImage = (val, key) => {
  const item = items.value.find((item) => item.key === key);
  if (item) {
    item.content = val.url || "";
  }
};
</script>
