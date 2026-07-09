<template>
    <div class="perms-manage">
        <el-card class="tree-card">
            <el-tree ref="treeRef" :data="treeData" :props="treeProps" node-key="id" highlight-current
                :filter-node-method="filterNode" :expand-on-click-node="false">
                <template #default="{ node, data }">
                    <div class="menu">
                        <div class="menu-left">
                            <el-icon v-if="data.parent_id == 0">
                                <House />
                            </el-icon>
                            {{ data.name }}
                            <span v-if="data.module != '' && data.controller != '' && data.action != ''">{{ data.module
                            }}/{{ data.controller }}/{{ data.action }}</span>
                            <el-tag type="info" size="small" v-if="data.status != 1">禁用</el-tag>
                        </div>
                        <div class=" menu-right">
                            <el-button type="primary" size="small" icon="Plus" @click="onVisibleAdd(data)"></el-button>
                            <el-button type="primary" size="small" icon="Edit" v-if="data.id > 0"
                                @click="onVisibleEdit(data)"></el-button>
                            <el-button type="danger" size="small" icon="Delete" v-if="data.id > 0"
                                @click="onDelete(data)"></el-button>
                        </div>
                    </div>
                </template>
            </el-tree>
        </el-card>


        <el-dialog v-model="dialogVisible" :title="dialogTitle" width="600px" destroy-on-close>
            <el-form ref="formRef" :model="formData" :rules="formRules" label-width="100px">
                <el-form-item label="上级节点" prop="parent_id">
                    <el-tree-select v-model="formData.parent_id" :data="treeData" :props="treeProps" check-strictly
                        clearable placeholder="请选择上级节点（留空为根节点）" style="width: 100%" />
                </el-form-item>

                <el-form-item label="节点名称" prop="name">
                    <el-input v-model="formData.name" placeholder="请输入节点名称" />
                </el-form-item>

                <el-form-item label="所属模块" prop="module">
                    <el-input v-model="formData.module" placeholder="请输入所属模块" />
                </el-form-item>

                <el-form-item label="控制器" prop="controller">
                    <el-input v-model="formData.controller" placeholder="请输入控制器" />
                </el-form-item>

                <el-form-item label="操作方法" prop="action">
                    <el-input v-model="formData.action" placeholder="请输入操作方法" />
                </el-form-item>

                <el-form-item label="排序" prop="sort">
                    <el-input-number v-model="formData.sort" :min="0" :max="9999" controls-position="right" />
                </el-form-item>

                <el-form-item label="状态" prop="status">
                    <el-switch v-model="formData.status" :active-value="1" :inactive-value="0" active-text="启用"
                        inactive-text="禁用" />
                </el-form-item>
            </el-form>
            <template #footer>
                <el-button @click="dialogVisible = false">取消</el-button>
                <el-button type="primary" @click="handleSubmit">确定</el-button>
            </template>
        </el-dialog>
    </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import perms from '@/api/admin/perms'


const dialogVisible = ref(false)
const dialogTitle = ref('添加节点')
const formRef = ref(null)
const loading = ref(false)

const formRules = {
    name: [{ required: true, message: '请输入节点名称', trigger: 'blur' }]
}

const formData = ref()
const onVisibleAdd = (item) => {
    dialogTitle.value = '添加节点'
    formData.value = {
        id: 0,
        parent_id: item.id,
        name: '',
        module: item.module,
        controller: item.controller,
        action: item.action,
        sort: 0,
        status: 1
    }
    dialogVisible.value = true
}
const onVisibleEdit = (item) => {
    dialogTitle.value = '编辑节点'
    formData.value = {
        id: item.id,
        parent_id: item.parent_id,
        name: item.name,
        module: item.module,
        controller: item.controller,
        action: item.action,
        sort: item.sort,
        status: item.status
    }
    dialogVisible.value = true
}

const handleSubmit = async () => {
    await formRef.value?.validate((valid) => {
        if (valid) {
            onAdd()
        }
    })
}
const onAdd = async () => {
    loading.value = true
    try {
        if (formData.value.id == 0) {
            await perms.add(formData.value)
            ElMessage.success("添加成功")
        } else {
            await perms.edit(formData.value)
            ElMessage.success("编辑成功")
        }
        dialogVisible.value = false
        getItems()
    } catch (error) {
    } finally {
        loading.value = false
    }
}
const treeData = ref([])


const treeProps = {
    children: 'children',
    label: 'name',
    value: 'id'
}

const treeRef = ref(null)

// 过滤节点
const filterNode = (value, data) => {
    if (!value) return true
    return data.name.includes(value)
}

// 删除节点
const onDelete = (data) => {
    ElMessageBox.confirm(
        `确定要删除节点 "${data.name}" 及其所有子节点吗？`,
        '删除确认',
        {
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning'
        }
    ).then(async () => {
        loading.value = true
        try {
            await perms.del({ ids: [data.id] })
            ElMessage.success("删除成功")
            getItems()
        } catch (error) {
        } finally {
            loading.value = false
        }
    }).catch(() => { })
}

const getItems = () => {
    const rootTree = [{
        id: 0,
        parent_id: 0,
        name: '根节点',
        module: '',
        controller: '',
        action: '',
        status: 1
    }]
    perms.getItems().then(data => {
        treeData.value = [...rootTree, ...data]
    })
}

onMounted(() => {
    getItems()
})



</script>

<style scoped lang="scss">
.menu {
    display: flex;
    width: 100%;


    &-left {
        flex: 1;
    }

}
</style>