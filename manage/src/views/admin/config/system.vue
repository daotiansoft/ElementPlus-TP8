<template>
    <div class="dashboard">
        <el-form ref="formRef" label-width="100px" style="max-width: 1000px" @keyup.enter="submitForm(formRef)">
            <el-form-item v-for="item in items" :label="item.name">
                <el-input v-if="item.type == 'text'" v-model="item.content" />
                <el-input v-if="item.type == 'textarea'" v-model="item.content" type="textarea" />
                <el-input v-if="item.type == 'image'" v-model="item.content">
                    <template #append>
                        <InputUpload @input="
                            (val) => {
                                setImage(val, item.key)
                            }
                        " />
                    </template>
                </el-input>
                <el-select v-if="item.type == 'select'" v-model="item.content">
                    <el-option v-for="option in item.params" :key="option.id" :label="option.name" :value="option.id" />
                </el-select>
                <div style="font-size: 12px; color: #909399">
                    {{ item.placeholder }}
                </div>
            </el-form-item>

            <el-form-item>
                <el-button type="primary" @click="submitForm(formRef)">保存</el-button>
            </el-form-item>
        </el-form>
    </div>
</template>
<script setup>
import InputUpload from '@/components/Upload/InputUpload.vue'
import { ElMessage } from 'element-plus'
import { ref, onMounted } from 'vue'

import config from '@/api/super/config'
const items = ref([])
const formRef = ref()


onMounted(() => {
    getItems()
})
const getItems = async () => {
    try {
        const data = await config.getItems()
        items.value = data
    } catch (error) {
        console.error('获取配置失败:', error)
    }
}
const submitForm = async (formEl) => {
    if (!formEl) return
    try {
        await config.postItems(items.value)
        ElMessage.success('保存配置成功')
    } catch (error) {
        console.error('保存配置失败:', error)
        ElMessage.error('保存配置失败')
    }
}
const setImage = (val, key) => {
    const item = items.value.find(item => item.key === key)
    if (item) {
        item.content = val.url || ''
    }
}
</script>