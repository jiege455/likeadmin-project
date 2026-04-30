<template>
    <el-card shadow="never" class="!border-none">
        <div class="mt-[-10px]">
            <el-tabs v-model="currentTabIndex">
                <el-tab-pane
                    v-for="(item, index) in tabsList"
                    :key="item.id"
                    :label="item.name"
                    :name="index"
                >
                    <component :is="item.component" v-model="item.data" />
                </el-tab-pane>
            </el-tabs>
        </div>
    </el-card>
    <footer-btns class="mt-4" :fixed="true">
        <el-button type="primary" @click="setData">保存</el-button>
    </footer-btns>
</template>
<script setup lang="ts">
import { markRaw } from 'vue'

import { getDecoratePages, setDecoratePages } from '@/api/decoration'

import MobileStyle from './components/mobile-style.vue'

const currentTabIndex = ref(0)
const tabsList = ref([
    {
        name: '移动端',
        id: 5,
        component: markRaw(MobileStyle),
        data: {
            themeColorId: 1,
            topTextColor: 'white',
            navigationBarColor: '',
            themeColor1: '#2F80ED',
            themeColor2: '#56CCF2',
            buttonColor: 'white'
        }
    }
])
const currentTab = computed(() => tabsList.value[currentTabIndex.value] || {})
//获取数据
const getData = async () => {
    try {
        const res = await getDecoratePages({ id: currentTab.value.id })
        // 检查后端返回的数据是否包含id，如果包含说明记录存在
        if (res.id) {
            // 确保当前 tab 的 id 与后端返回的一致
            currentTab.value.id = res.id
            if (res.data) {
                const parsedData = JSON.parse(res.data)
                currentTab.value.data = { ...currentTab.value.data, ...parsedData }
            }
        } else {
            console.warn('警告：后端返回数据不包含ID，可能记录不存在')
        }
    } catch (e) {
        console.error('获取风格配置失败:', e)
    }
}

//保存数据
const setData = async () => {
    try {
        await setDecoratePages({
            id: currentTab.value.id, // 使用从后端获取确认过的 ID
            type: 5, // 明确指定 type 为 5 (系统风格)
            data: JSON.stringify(currentTab.value.data)
        })
        getData()
    } catch (e) {
        console.error('保存风格配置失败:', e)
    }
}

//初始化数据
onMounted(async () => {
    await getData()
})
</script>
