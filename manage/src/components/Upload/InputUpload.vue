<template>
  <el-upload
    ref="uploadRef"
    :action="uploadInfo.action"
    :headers="uploadInfo.headers"
    :multiple="false"
    name="image"
    :show-file-list="false"
    :on-success="handleSuccess"
    :on-error="handleError"
    :before-upload="beforeUpload"
    :disabled="!uploadInfo.action"
  >
    <slot>
      <el-button type="primary">点击上传</el-button>
    </slot>
  </el-upload>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { ElMessage } from "element-plus";
import user from "@/api/user";

// 定义组件事件
const emit = defineEmits(["input"]);

// 上传配置信息
const uploadInfo = ref({
  action: "",
  headers: {},
});

// 上传组件引用
const uploadRef = ref(null);

// 获取上传凭证
const fetchUploadInfo = async () => {
  try {
    const data = await user.getUploadInfo();
    uploadInfo.value = {
      action: data.action || "",
      headers: data.headers || {},
    };
  } catch (error) {
    ElMessage.error("获取上传配置失败，请稍后重试");
    console.error("获取上传信息失败:", error);
  }
};

// 上传成功回调
const handleSuccess = (response) => {
  if (!response || !response.data || !response.data.url) {
    ElMessage.error("上传失败，未返回有效的文件信息");
    return;
  }
  emit("input", response.data);
};

// 上传失败回调
const handleError = (error) => {
  ElMessage.error("文件上传失败，请稍后重试");
  console.error("上传失败:", error);
};

// 上传前校验（可选）
const beforeUpload = (file) => {
  // 可以在此处添加文件类型/大小校验
  // 示例：限制图片类型
  // const isImage = file.type.startsWith('image/')
  // if (!isImage) {
  //   ElMessage.error('只能上传图片文件')
  //   return false
  // }
  // 示例：限制大小 5MB
  // const isLt5M = file.size / 1024 / 1024 < 5
  // if (!isLt5M) {
  //   ElMessage.error('图片大小不能超过 5MB')
  //   return false
  // }
  return true;
};

// 组件挂载时获取上传信息
onMounted(() => {
  fetchUploadInfo();
});

// 暴露刷新上传配置的方法（可选）
defineExpose({
  refresh: fetchUploadInfo,
});
</script>

<style scoped>
/* 可自定义样式，无强制要求 */
</style>
