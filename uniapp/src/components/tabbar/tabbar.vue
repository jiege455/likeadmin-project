<template>
    <u-tabbar
        v-if="showTabbar"
        v-model="current"
        :active-color="activeColor"
        :inactive-color="inactiveColor"
        :list="tabbarList"
        @change="handleChange"
        :hide-tab-bar="true"
    ></u-tabbar>
</template>

<script lang="ts" setup>
import { useAppStore } from '@/stores/app'
import { navigateTo } from '@/utils/util'
import { computed, ref, onMounted } from 'vue'
import { onShow } from '@dcloudio/uni-app'

const current = ref()
const appStore = useAppStore()

onMounted(() => {
    uni.hideTabBar()
})

onShow(() => {
    uni.hideTabBar()
})

const tabbarList = computed(() => {
    if (!appStore.isConfigLoaded) return []
    return appStore.getTabbarConfig
        ?.filter((item: any) => item.is_show == 1)
        .map((item: any) => {
            return {
                iconPath: item.unselected,
                selectedIconPath: item.selected,
                text: item.name,
                link: item.link,
                pagePath: item.link.path
            }
        })
})

const showTabbar = computed(() => {
    if (!appStore.isConfigLoaded) return false

    const currentPages = getCurrentPages()
    const currentPage = currentPages[currentPages.length - 1]
    if (!currentPage) return false

    const route = currentPage.route

    const index = tabbarList.value.findIndex((item: any) => {
        return item.pagePath === '/' + route || item.pagePath === route
    })

    if (index >= 0) {
        current.value = index
        return true
    }
    return false
})

const activeColor = computed(() => appStore.getStyleConfig.selected_color || '#2979ff')
const inactiveColor = computed(() => appStore.getStyleConfig.default_color || '#909399')

const handleChange = (index: number) => {
    const selectTab = tabbarList.value[index]
    if (!selectTab) return

    // 强制设置 canTab 为 false，确保使用 reLaunch 跳转，避免因页面未在原生 tabBar 定义导致 switchTab 失败
    const link = { ...selectTab.link, canTab: false }
    navigateTo(link, 'reLaunch')
}
</script>
