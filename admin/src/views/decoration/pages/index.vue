<template>
    <div class="decoration-pages">
        <div class="flex flex-1 h-full justify-between">
            <el-card
                shadow="never"
                class="!border-none flex scroll-view-content flex-shrink-0"
                :body-style="{ 'padding-right': 0 }"
            >
                <Menu v-model="activeMenu" :menus="menus" @add="handleAddPage" @delete="handleDeletePage" />
            </el-card>

            <div class="flex-1 flex flex-col scroll-view-content overflow-hidden min-w-0 relative">
                <preview
                    class="flex-1 h-0"
                    v-model="selectWidgetIndex"
                    @updatePageData="updatePageData"
                    :pageData="getPageData"
                    :pageMeta="getPageMeta"
                />
                
                <!-- 添加组件面板（悬浮抽屉式） -->
                <el-drawer
                    v-model="drawerVisible"
                    title="组件库"
                    direction="ltr"
                    :size="300"
                    :modal="false"
                    :lock-scroll="false"
                    :append-to-body="true"
                    custom-class="widget-drawer"
                >
                    <add-widgets
                        v-if="showAddWidgets"
                        :page-type="getPageType"
                        @add="handleAddWidget"
                    />
                </el-drawer>

                <!-- 悬浮按钮 -->
                <div 
                    class="absolute left-4 top-4 z-50 bg-white p-2 rounded-full shadow-lg cursor-pointer hover:bg-gray-50 text-primary"
                    @click="drawerVisible = true"
                    v-if="!drawerVisible && showAddWidgets"
                >
                    <el-icon :size="24"><Plus /></el-icon>
                    <span class="ml-1 text-sm font-medium">添加组件</span>
                </div>
            </div>

            <attr-setting
                class="w-[400px] scroll-view-content hidden lg:block flex-shrink-0"
                :widget="getSelectWidget"
                @update:content="updateContent"
            />
        </div>
        <footer-btns class="mt-4 z-50" :fixed="false" v-perms="['decorate:pages:save']">
            <el-button type="primary" @click="setData">保存</el-button>
        </footer-btns>

        <!-- 新增页面弹窗 -->
        <el-dialog v-model="showAddDialog" title="新建页面" width="500px">
            <el-form :model="addForm" label-width="80px">
                <el-form-item label="页面名称" required>
                    <el-input v-model="addForm.name" placeholder="请输入页面名称"></el-input>
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="showAddDialog = false">取消</el-button>
                    <el-button type="primary" @click="confirmAddPage">确定</el-button>
                </span>
            </template>
        </el-dialog>
    </div>
</template>
<script lang="ts" setup name="decorationPages">
import { getDecoratePages, setDecoratePages, getDecoratePageList, addDecoratePage, delDecoratePage } from '@/api/decoration'
import { getNonDuplicateID } from '@/utils/util'
import { ElMessageBox, ElMessage } from 'element-plus'
import { Plus } from '@element-plus/icons-vue' // 引入图标

import AttrSetting from '../component/pages/attr-setting.vue'
import Menu from '../component/pages/menu.vue'
import Preview from '../component/pages/preview.vue'
import widgets from '../component/widgets'

import AddWidgets from '../component/add-widgets.vue'

enum pagesTypeEnum {
    HOME = '1',
    USER = '2',
    SERVICE = '3',
    ORDER = '4',
    MERCHANT = '5',
    DISTRIBUTION = '6',
    INVITE = '7',
    WALLET = '8',
    COUPON = '9'
}

const drawerVisible = ref(false) // 默认关闭，避免遮挡界面导致卡顿感

const updatePageData = (value: any) => {
    menus[activeMenu.value].pageData = [...value]
}

const generatePageData = (widgetNames: string[]) => {
    return widgetNames.map((widgetName) => {
        const widget = widgets[widgetName]
        let defOptions = {}
        if (widget) {
            if (typeof widget.options === 'function') {
                defOptions = widget.options()
            } else if (widget.options) {
                defOptions = widget.options
            }
        }
        
        const options = {
            id: getNonDuplicateID(),
            ...defOptions
        }
        return options
    })
}

const handleAddWidget = (widgetName: string) => {
    const widget = widgets[widgetName]
    if (!widget) {
        return
    }
    
    // 获取默认配置
    let defOptions = {}
    if (typeof widget.options === 'function') {
        defOptions = widget.options()
    } else if (widget.options) {
        defOptions = widget.options
    }

    const options = {
        id: getNonDuplicateID(),
        ...defOptions
    }
    
    if (!menus[activeMenu.value].pageData) {
        menus[activeMenu.value].pageData = []
    }
    
    menus[activeMenu.value].pageData.push(options)
    selectWidgetIndex.value = menus[activeMenu.value].pageData.length - 1
}

const updateContent = (content: any) => {
    if (selectWidgetIndex.value === -1) {
        // 更新 pageMeta (页面设置)
        if (menus[activeMenu.value]?.pageMeta) {
            // 确保 pageMeta 数组存在
            if (!menus[activeMenu.value].pageMeta.length) {
                menus[activeMenu.value].pageMeta = generatePageData(['page-meta'])
            }
            menus[activeMenu.value].pageMeta[0].content = content
            // 同步更新菜单名称
            if (content.title) {
                menus[activeMenu.value].name = content.title
            }
        }
    } else {
        // 更新普通组件
        if (menus[activeMenu.value]?.pageData) {
            menus[activeMenu.value].pageData[selectWidgetIndex.value].content = content
        }
    }
}

const menus: Record<
    string,
    {
        id: number
        name: string
        pageMeta?: any
        pageData: any[]
        type?: number
    }
> = reactive({
    [pagesTypeEnum.HOME]: {
        id: 1,
        type: 1,
        name: '',
        pageMeta: generatePageData(['page-meta']),
        pageData: []
    },
    [pagesTypeEnum.USER]: {
        id: 2,
        type: 2,
        name: '',
        pageMeta: generatePageData(['page-meta']),
        pageData: []
    }
})

const activeMenu = ref<string>('1')
const selectWidgetIndex = ref<number>(-1)
const getPageData = computed(() => {
    return menus[activeMenu.value]?.pageData ?? []
})
const getPageMeta = computed(() => {
    return menus[activeMenu.value]?.pageMeta ?? null
})
const getSelectWidget = computed(() => {
    if (selectWidgetIndex.value === -1) {
        return menus[activeMenu.value]?.pageMeta?.[0] ?? ''
    } else {
        return menus[activeMenu.value]?.pageData[selectWidgetIndex.value] ?? ''
    }
})

const getPageType = computed(() => {
    // 如果是自定义页面(type=10)，返回一个通用的类型（如'1'），以便加载所有组件
    const current = menus[activeMenu.value]
    if (current && current.type == 10) {
        return '1' // 复用首页的组件列表，或者在 add-widgets 中定义 '10'
    }
    return activeMenu.value
})

const showAddWidgets = computed(() => {
    // 允许显示的页面类型
    const allowed = [
        pagesTypeEnum.HOME, 
        pagesTypeEnum.USER, 
        pagesTypeEnum.ORDER, 
        pagesTypeEnum.MERCHANT, 
        pagesTypeEnum.DISTRIBUTION, 
        pagesTypeEnum.INVITE, 
        pagesTypeEnum.WALLET, 
        pagesTypeEnum.COUPON
    ]
    const current = menus[activeMenu.value]
    // 如果是自定义页面也显示
    if (current && current.type == 10) return true
    return allowed.includes(activeMenu.value as any)
})

const getData = async () => {
    try {
        const current = menus[activeMenu.value]
        
        if (!current) return 

        const data = await getDecoratePages({ id: activeMenu.value })
        if (data.data) {
            const parsedData = JSON.parse(data.data)
            menus[String(data.id)].pageData = Array.isArray(parsedData) ? parsedData : []
        } else {
            menus[String(data.id)].pageData = []
        }
        
        menus[String(data.id)].pageMeta = data?.meta ? JSON.parse(data?.meta) : menus[String(data.id)].pageMeta
        
        // 【修复】优先使用后端返回的 name 字段，确保名称一致性
        if (data.name) {
            menus[String(data.id)].name = data.name
        } else {
            // 如果后端没有 name，尝试从 pageMeta 中获取
            const pageMeta = menus[String(data.id)].pageMeta
            if (pageMeta?.[0]?.content?.title) {
                menus[String(data.id)].name = pageMeta[0].content.title
            }
        }
        
        // 兼容旧数据：如果没有 title_text，则使用 title 作为标题文字
        const pageMeta = menus[String(data.id)].pageMeta
        if (pageMeta?.[0]?.content?.title && !pageMeta[0].content.title_text) {
            pageMeta[0].content.title_text = pageMeta[0].content.title
        }
    } catch (e) {
        console.error('获取装修数据失败:', e)
        if (menus[activeMenu.value]) {
            menus[activeMenu.value].pageData = []
        }
    }
}

// 获取自定义页面列表并合并
const getCustomPages = async () => {
    try {
        const res = await getDecoratePageList()
        if (res) {
            res.forEach((item: any) => {
                // 排除 PC 首页 (type=4) 和 系统风格 (type=5)
                if (item.type == 4 || item.type == 5) return

                // 【修复】如果是首页(type=1)或个人中心(type=2)，更新预设菜单而不是覆盖
                if (item.type == 1 || item.type == 2) {
                    const menuKey = String(item.type)
                    if (menus[menuKey]) {
                        menus[menuKey].id = item.id
                        menus[menuKey].name = item.name || menus[menuKey].name
                        menus[menuKey].type = item.type
                    }
                    return
                }

                // 其他类型作为自定义页面处理
                const meta = JSON.parse(item.meta || '{}')
                menus[String(item.id)] = {
                    id: item.id,
                    type: item.type,
                    name: meta.name || item.name || `自定义页面${item.id}`,
                    pageMeta: generatePageData(['page-meta']),
                    pageData: []
                }
            })
        }
    } catch (e) {
        console.error(e)
    }
}

const setData = async () => {
    const data = menus[activeMenu.value]
    
    // 如果是自定义页面，需要把名称存入 meta
    if (data.type == 10) {
        if (!data.pageMeta) data.pageMeta = generatePageData(['page-meta'])
        // 确保 meta 中有 title
        // data.pageMeta[0].content.title = data.name 
    }

    await setDecoratePages({
        ...data,
        name: data.name, // 明确传递页面名称，以便后端更新
        data: JSON.stringify(data.pageData),
        meta: data?.pageMeta ? JSON.stringify(data?.pageMeta) : null
    })
    
    if (data.type == 10) {
        // 自定义页面保存后刷新列表，以防万一
        await getCustomPages()
    }
    
    getData()
}

// 新增页面逻辑
const showAddDialog = ref(false)
const addForm = reactive({
    name: ''
})

const handleAddPage = () => {
    addForm.name = ''
    showAddDialog.value = true
}

const confirmAddPage = async () => {
    if (!addForm.name) {
        ElMessage.warning('请输入页面名称')
        return
    }
    try {
        await addDecoratePage({
            type: 10,
            name: addForm.name,
            data: '[]',
            meta: JSON.stringify({ name: addForm.name })
        })
        ElMessage.success('创建成功')
        showAddDialog.value = false
        await getCustomPages()
        // 自动切换到新页面? 需要知道新ID，暂时略过
    } catch (e) {
        console.error(e)
    }
}

// 删除页面逻辑
const handleDeletePage = (id: number) => {
    // 再次确认不可删除核心页面
    if (id <= 4) {
        ElMessage.warning('系统核心页面不可删除')
        return
    }
    
    ElMessageBox.confirm(
        '确定要删除该页面吗？删除后不可恢复，且可能影响跳转链接。',
        '删除确认',
        {
            confirmButtonText: '删除',
            cancelButtonText: '取消',
            type: 'warning',
        }
    ).then(async () => {
        try {
            await delDecoratePage({ id })
            ElMessage.success('删除成功')
            // 如果当前选中是删除的页面，切回首页
            if (activeMenu.value == String(id)) {
                activeMenu.value = '1'
            }
            delete menus[String(id)]
        } catch (e) {
            console.error(e)
        }
    })
}

watch(
    activeMenu,
    async () => {
        // 【修复】如果 menus 中不存在（可能是刚加载），先尝试加载列表
        // ID > 2 的都可能是自定义页面，不一定非要 > 100
        // 因为删除了中间的预设页面后，新 ID 可能从 6 开始
        if (!menus[activeMenu.value] && Number(activeMenu.value) > 2) {
             await getCustomPages()
        }
        
        if (menus[activeMenu.value]) {
            selectWidgetIndex.value = getPageData.value.findIndex((item) => !item.disabled)
            getData()
        }
    },
    {
        immediate: true
    }
)

// 初始化
getCustomPages().then(() => {
// 如果初始 activeMenu 是自定义页面，需要刷新数据
if (Number(activeMenu.value) > 2) {
    getData()
}
})
</script>
<style lang="scss" scoped>
$scroll-height: calc(100vh - var(--navbar-height) - 74px);
.decoration-pages {
    height: $scroll-height;
    @apply flex flex-col;
    .scroll-view-content {
        height: calc($scroll-height - 60px);
    }
}
</style>
