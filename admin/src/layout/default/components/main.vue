<template>
    <main class="main-wrap h-full bg-page">
        <el-scrollbar
            :native="false"
            :noresize="false"
            :tag="'div'"
            :view-class="'main-scroll-view'"
        >
            <div class="p-4">
                <router-view v-if="isRouteShow" v-slot="{ Component, route }">
                    <keep-alive :include="includeList" :max="15">
                        <component :is="Component" :key="route.name || route.path" />
                    </keep-alive>
                </router-view>
            </div>
        </el-scrollbar>
    </main>
</template>

<script setup lang="ts">
/**
 * 开发者公众号：杰哥网络科技
 * qq2711793818 杰哥
 */
import useAppStore from '@/stores/modules/app'
import useTabsStore from '@/stores/modules/multipleTabs'
import useSettingStore from '@/stores/modules/setting'

const appStore = useAppStore()
const tabsStore = useTabsStore()
const settingStore = useSettingStore()
const isRouteShow = computed(() => appStore.isRouteShow)
const includeList = computed(() => (settingStore.openMultipleTabs ? tabsStore.getCacheTabList : []))
</script>

<style>
.main-wrap {
    will-change: transform;
}

.main-wrap :deep(.el-scrollbar__wrap) {
    overflow-x: hidden;
    will-change: scroll-position;
}

.main-wrap :deep(.el-scrollbar__view) {
    will-change: transform;
}

.main-scroll-view {
    min-height: 100%;
    will-change: transform;
    transform: translateZ(0);
}
</style>
