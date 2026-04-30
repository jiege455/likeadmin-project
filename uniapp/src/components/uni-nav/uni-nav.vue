<template>
    <view
        class="uni-nav z-50"
        :class="{ 'fixed top-0 left-0 right-0': fixed }"
        :style="navStyle"
    >
        <view class="status-bar" :style="{ height: statusBarHeight + 'px' }"></view>
        <view class="nav-content flex items-center justify-between px-3 relative" :style="{ height: navContentHeight + 'px' }">
            <view
                class="nav-left flex items-center h-full min-w-[80px]"
                @click="handleBack"
                v-if="showBack"
            >
                <u-icon :name="backIcon" :size="backIconSize" :color="textColor"></u-icon>
                <text v-if="backText" class="ml-1 text-sm" :style="{ color: textColor }">{{
                    backText
                }}</text>
            </view>
            <view v-else class="nav-left min-w-[80px]"></view>

            <view
                class="nav-title absolute left-0 right-0 flex items-center justify-center h-full pointer-events-none"
                :style="{ paddingHorizontal: titlePadding + 'px' }"
            >
                <text
                    class="font-bold text-base truncate max-w-full"
                    :style="{ color: textColor, fontSize: titleSize + 'rpx' }"
                >
                    {{ title }}
                </text>
            </view>

            <view class="nav-right flex items-center h-full min-w-[80px]">
                <slot name="right"></slot>
            </view>
        </view>
    </view>
    <view v-if="fixed" class="nav-placeholder" :style="{ height: navHeight + 'px' }"></view>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useThemeStore } from '@/stores/theme'
import { safeNavigateBack } from '@/utils/util'

export interface UniNavProps {
    title?: string
    showBack?: boolean
    backText?: string
    backIcon?: string
    backIconSize?: number
    bgColor?: string
    textColor?: string
    fixed?: boolean
    titleSize?: number
    customBack?: () => void
    defaultUrl?: string
}

const props = withDefaults(defineProps<UniNavProps>(), {
    title: '',
    showBack: true,
    backText: '',
    backIcon: 'arrow-left',
    backIconSize: 20,
    bgColor: '',
    textColor: '',
    fixed: true,
    titleSize: 32,
    customBack: undefined,
    defaultUrl: '/pages/index/index'
})

const themeStore = useThemeStore()

const systemInfo = uni.getSystemInfoSync()
const statusBarHeight = systemInfo.statusBarHeight || 20
const navContentHeight = 40
const navHeight = computed(() => navContentHeight + statusBarHeight)

const titlePadding = computed(() => {
    return 80
})

const navStyle = computed(() => {
    const bgColor = props.bgColor || themeStore.navBgColor || '#ffffff'
    const textColor = props.textColor || themeStore.navColor || '#333333'
    return {
        backgroundColor: bgColor,
        color: textColor
    }
})

const handleBack = () => {
    if (typeof props.customBack === 'function') {
        props.customBack()
        return
    }
    safeNavigateBack({ defaultUrl: props.defaultUrl })
}
</script>

<script lang="ts">
export default {
    name: 'UniNav',
    options: {
        virtualHost: true,
        styleIsolation: 'shared'
    }
}
</script>

<style lang="scss" scoped>
.uni-nav {
    width: 100%;
    box-sizing: border-box;

    &.fixed {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
    }
}

.status-bar {
    width: 100%;
}

.nav-content {
    position: relative;
    display: flex;
    align-items: center;
}

.nav-left {
    z-index: 10;
}

.nav-title {
    z-index: 1;
}

.nav-right {
    z-index: 10;
}

.nav-placeholder {
    width: 100%;
}
</style>