<template>
  <div class="login-container">
    <el-form
      ref="formRef"
      :model="form"
      :rules="formRules"
      label-width="0"
      class="login-form"
      @keyup.enter="submitForm(formRef)"
    >
      <div class="title-container">
        <h3 class="title">{{ website.site_name }}</h3>
      </div>
      <!-- 账号 -->
      <el-form-item prop="username">
        <el-input
          v-model="form.username"
          placeholder="请输入账号"
          size="large"
          :prefix-icon="User"
          clearable
        />
      </el-form-item>

      <!-- 密码 -->
      <el-form-item prop="password">
        <el-input
          v-model="form.password"
          type="password"
          placeholder="请输入密码"
          size="large"
          :prefix-icon="Lock"
          show-password
          clearable
        />
      </el-form-item>

      <!-- 图片验证码 -->
      <el-form-item prop="captcha" v-if="website.captcha_status === 1">
        <div class="captcha-wrapper">
          <el-input
            v-model="form.captcha"
            placeholder="请输入验证码"
            size="large"
            :prefix-icon="Stamp"
            clearable
            class="captcha-input"
          />
          <div class="captcha-image" @click="onCaptcha">
            <img :src="captcha_image" alt="验证码" :title="'点击更换验证码'" />
          </div>
        </div>
      </el-form-item>

      <!-- 登录按钮 -->
      <el-form-item>
        <el-button
          type="primary"
          size="large"
          @click="submitForm(formRef)"
          class="login-btn"
          v-loading="loading"
        >
          登 录
        </el-button>
      </el-form-item>
    </el-form>
  </div>
</template>
<script setup>
import { ref, reactive, onMounted } from "vue";
import { useRouter } from "vue-router";
import { User, Lock, Stamp } from "@element-plus/icons-vue";

import overt from "@/api/overt";
import { useUserStore } from "@/store/modules/user";

const router = useRouter();
const userStore = useUserStore();

const formRef = ref();
const form = ref({
  username: "",
  password: "",
  captcha: "",
  captcha_key: "",
});
const formRules = reactive({
  username: [{ required: true, message: "请输入账号", trigger: "blur" }],
  password: [{ required: true, message: "请输入密码", trigger: "blur" }],
});
const captcha_image = ref("");
const loading = ref(false);

const website = ref({
  site_name: "管理面板",
  captcha_status: 2,
});
const onCaptcha = () => {
  overt.getCaptcha().then((data) => {
    form.value.captcha_key = data.key;
    captcha_image.value = data.data;
  });
};

const submitForm = async (formEl) => {
  if (!formEl) return;
  await formEl.validate((valid, fields) => {
    if (valid) {
      login();
    } else {
      console.log("error submit!", fields);
    }
  });
};
const login = async () => {
  loading.value = true;
  try {
    const data = await overt.postLogin(form.value);
    userStore.signIn(data.token);
    ElMessage.success("登录成功");
    router.push({ path: "/" });
  } catch (err) {
    console.error("登录失败", err);
    // 验证码通常一次性有效，登录失败后需要换一张
    if (website.value.captcha_status === 1) {
      onCaptcha();
    }
  } finally {
    loading.value = false;
  }
};

onMounted(async () => {
  try {
    website.value = await overt.init();
  } catch (error) {
    console.error("获取站点信息失败", error);
  }
  // 未开启验证码时不请求验证码图片
  if (website.value.captcha_status === 1) {
    onCaptcha();
  }
});
</script>
<style scoped lang="scss">
$light_gray: #eee;

.login-container {
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 100vh;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 20px;
}

.title-container {
  position: relative;

  .title {
    font-size: 26px;
    color: $light_gray;
    margin: 0px auto 40px auto;
    text-align: center;
    font-weight: bold;
  }
}

.login-form {
  margin-top: 10px;
}

@media screen and (min-width: 500px) {
  .login-form {
    min-width: 450px;
  }
}

@media screen and (max-width: 500px) {
  .login-form {
    min-width: 90%;
  }
}

.login-form :deep(.el-input__wrapper) {
  border-radius: 8px;
  transition: all 0.3s ease;
}

.login-form :deep(.el-input__wrapper:hover) {
  box-shadow: 0 0 0 1px #409eff inset;
}

.login-form :deep(.el-input__wrapper.is-focus) {
  box-shadow: 0 0 0 2px rgba(64, 158, 255, 0.2) inset;
}

.login-form :deep(.el-form-item) {
  margin-bottom: 22px;
}

.captcha-wrapper {
  display: flex;
  gap: 12px;
  align-items: center;
}

.captcha-input {
  flex: 1;
}

.captcha-image {
  flex-shrink: 0;
  cursor: pointer;
  border-radius: 6px;
  overflow: hidden;
  border: 1px solid #e6e6e6;
  line-height: 0;
  transition: border-color 0.3s ease;
}

.captcha-image:hover {
  border-color: #409eff;
}

.captcha-image img {
  display: block;
  width: 200px;
  height: 50px;
  object-fit: cover;
}

.login-btn {
  width: 100%;
  border-radius: 8px;
  font-size: 16px;
  font-weight: 500;
  height: 48px;
  transition: all 0.3s ease;
}

.login-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(64, 158, 255, 0.4);
}
</style>
