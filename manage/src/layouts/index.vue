<!-- src/layouts/Layout.vue -->
<template>
    <el-container class="layout-container">
        <el-aside :width="isCollapse ? '64px' : '220px'" class="aside-container">
            <!-- Logo -->
            <div class="logo-wrapper">
                <img v-if="websiteInfo?.logo" :src="websiteInfo?.logo" alt="Logo" class="logo-img" />
                <span v-show="!isCollapse" class="logo-text">{{ websiteInfo?.site_name }}</span>

            </div>

            <!-- 菜单 -->
            <el-menu :default-active="activeMenu" :default-openeds="defaultOpeneds" :collapse="isCollapse"
                :collapse-transition="false" background-color="#001428" text-color="#bfcbd9" active-text-color="#409EFF"
                router class="menu-container">
                <MenuItem v-for="route in menuRoutes" :key="route.path" :route="route" />
            </el-menu>
        </el-aside>

        <!-- 右侧容器 -->
        <el-container>
            <!-- 顶部栏 -->
            <el-header height="60px" class="header-container">
                <!-- 左侧：控制按钮 + 面包屑 -->
                <div class="header-left">
                    <!-- 菜单折叠控制按钮 -->
                    <el-tooltip :content="isCollapse ? '展开菜单' : '折叠菜单'" placement="bottom">
                        <el-icon :size="20" @click="toggleCollapse" style="cursor: pointer">
                            <Expand v-if="isCollapse" />
                            <Fold v-else />
                        </el-icon>
                    </el-tooltip>

                    <el-breadcrumb separator="/" class="breadcrumb">
                        <el-breadcrumb-item :to="{ path: '/' }">首页</el-breadcrumb-item>
                        <el-breadcrumb-item v-for="(item, index) in breadcrumbs" :key="index">
                            {{ item.meta?.title || item.name }}
                        </el-breadcrumb-item>
                    </el-breadcrumb>
                </div>

                <!-- 右侧：用户信息 -->
                <div class="header-right">
                    <el-dropdown @command="handleCommand">
                        <div class="user-info">
                            <el-avatar :size="32" v-if="userInfo?.avatar" :src="userInfo?.avatar" />
                            <span class="username">{{ userInfo?.nickname }}</span>
                            <el-icon>
                                <ArrowDown />
                            </el-icon>
                        </div>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item command="password">修改密码</el-dropdown-item>
                                <el-dropdown-item divided command="logout">退出登录</el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                </div>
            </el-header>

            <!-- 主体内容 -->
            <el-main class="main-container">
                <router-view />
            </el-main>
        </el-container>
    </el-container>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { Expand, Fold, ArrowDown } from '@element-plus/icons-vue'
import MenuItem from './MenuItem.vue'
import user from '@/api/user'
import transformRoutes from '@/router/routes'

import { useUserStore } from '@/store/modules/user'
import { useOvertStore } from '@/store/modules/overt'
import { inputNumberEmits } from 'element-plus'

const userStore = useUserStore()
const overtStore = useOvertStore()

const userInfo = ref({})
const websiteInfo = ref({})

const route = useRoute()
const router = useRouter()

// 菜单折叠状态
const isCollapse = ref(false)

// 当前激活的菜单路径
const activeMenu = computed(() => {
    // 如果有自定义高亮路径，使用它
    if (route.meta?.activeMenu) {
        return route.meta.activeMenu
    }
    return route.path
})

// 默认展开的菜单（根据当前路由自动展开）
const defaultOpeneds = computed(() => {
    const paths = route.path.split('/')
    const openPaths = []
    let currentPath = ''

    // 从根路径开始，逐级构建父路径
    for (let i = 1; i < paths.length - 1; i++) {
        currentPath += '/' + paths[i]
        openPaths.push(currentPath)
    }

    return openPaths
})

const buildMenuTree = async () => {
    const routeItems = await userStore.getRoutesItems()
    return transformRoutes(routeItems)
}

const menuRoutes = ref({})


// 面包屑数据
const breadcrumbs = ref([])

// 更新面包屑
const updateBreadcrumbs = () => {
    const matched = route.matched
    breadcrumbs.value = matched.filter(item =>
        item.path !== '/' && !item.meta?.hidden
    )
}

// 监听路由变化更新面包屑
watch(() => route.path, () => {
    updateBreadcrumbs()
}, { immediate: true })

// 切换菜单折叠
const toggleCollapse = () => {
    isCollapse.value = !isCollapse.value
}

// 下拉菜单命令处理
const handleCommand = (command) => {
    if (command === 'logout') {
        ElMessageBox.confirm('确定要退出登录吗？', '提示', {
            confirmButtonText: '确定',
            cancelButtonText: '取消'
        })
            .then(() => {
                user.logout().then(() => {
                    localStorage.removeItem('token')
                    router.push('/login')
                }).catch(err => {
                    console.error('退出登录失败', err)
                })

            })
            .catch(() => {
                // catch error
            })

    }
    if (command === 'password') {
        router.push('/center/password')
    }
}

onMounted(async () => {
    userStore.getUserInfo().then(res => {
        userInfo.value = res
    }).catch(err => {
        console.error('获取用户信息失败', err)
    })
    overtStore.getWebsite().then(res => {
        websiteInfo.value = res
    }).catch(err => {
        console.error('获取网站信息失败', err)
    })

    //动态更新菜单
    let routes = router.options.routes
    const routeItems = transformRoutes(await userStore.getRoutesItems())
    routeItems.forEach(routeItem => {
        const hasPath = routes.some(item => item.path === routeItem.path)
        if (!hasPath) {
            routes.push(routeItem)
        }

    });
    menuRoutes.value = routes.filter(r => r.path !== '/')
})



</script>

<style scoped>
.layout-container {
    height: 100vh;
}

/* ===== 侧边栏样式 ===== */
.aside-container {
    background-color: #001428;
    overflow: hidden;
    transition: width 0.3s;
    flex-shrink: 0;
}

.logo-wrapper {
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    border-bottom: 1px solid #1f2d3d;
    flex-shrink: 0;
}

.logo-img {
    width: 32px;
    height: 32px;
    flex-shrink: 0;
}

.logo-text {
    color: #fff;
    font-size: 18px;
    font-weight: bold;
    white-space: nowrap;
}

.menu-container {
    border-right: none;
    height: calc(100vh - 60px);
    overflow-y: auto;
}

.menu-container::-webkit-scrollbar {
    width: 4px;
}

.menu-container::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 2px;
}

/* ===== 顶部栏样式 ===== */
.header-container {
    background: #fff;
    border-bottom: 1px solid #e6e6e6;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 16px;
}

.breadcrumb {
    font-size: 14px;
}

.header-right {
    display: flex;
    align-items: center;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 4px;
    transition: background 0.2s;
}

.user-info:hover {
    background: #f5f7fa;
}

.username {
    font-size: 14px;
    color: #333;
}

/* ===== 主体内容样式 ===== */
.main-container {
    background: #f0f2f5;
    padding: 20px;
    overflow-y: auto;
}
</style>