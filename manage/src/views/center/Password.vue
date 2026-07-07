<template>
    <div class="app-container">
        <el-form ref="formRef" :model="form" label-width="120px" :rules="formRoule" @keyup.enter="submitForm(formRef)">
            <el-form-item label="原密码" prop="password_old">
                <el-input v-model="form.password_old" placeholder="请输入原密码，留空不修改" />
            </el-form-item>
            <el-form-item label="新密码" prop="password_new">
                <el-input v-model="form.password_new" placeholder="请输入不少于6位新密码，留空不修改" />
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="submitForm(formRef)">提交</el-button>
            </el-form-item>
        </el-form>
    </div>
</template>
<script setup>
import { ref, reactive } from 'vue'

import user from '@/api/user'

const formRef = ref()
const form = ref({
    password_old: '',
    password_new: ''
})
const formRoule = reactive({
    password_old: [
        { required: false, message: '请输入原密码', trigger: 'blur' }
    ],
    password_new: [
        { required: false, message: '请输入新密码', trigger: 'blur' },
        { min: 6, message: '新密码不能少于6位', trigger: 'blur' }
    ]
})
const loading = ref(false)

const submitForm = async (formEl) => {
    if (!formEl) return
    await formEl.validate((valid, fields) => {
        if (valid) {
            submit()
        } else {
            console.log('error submit!', fields)
        }
    })
}
const submit = async () => {
    loading.value = true
    try {
        await user.postPassword(form.value).then(data => {
            loading.value = false
            ElMessage.success('修改密码成功')
        })
    } catch (err) {
        loading.value = false
        console.error('修改密码失败', err)
    }
}
</script>