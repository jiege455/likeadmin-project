<template>
    <view class="user-info mb-[0rpx]" :style="bgStyle">
        <!-- #ifndef H5 -->
        <u-sticky h5-nav-height="0" bg-color="transparent">
            <u-navbar
                :is-back="false"
                :is-fixed="false"
                :title="metaData.title"
                :custom-title="metaData.title_type == 2"
                :border-bottom="false"
                :title-bold="true"
                :background="{ background: 'rgba(256,256, 256, 0)' }"
                :title-color="content.text_color || '#ffffff'"
            >
                <template #title>
                    <image class="!h-[54rpx]" :src="metaData.title_img" mode="widthFix"></image>
                </template>
            </u-navbar>
        </u-sticky>
        <!-- #endif -->

        <!-- #ifdef H5 -->
        <view class="pt-[40rpx]"></view>
        <!-- #endif -->
        <view class="flex items-center justify-between px-[50rpx] pb-[50rpx] pt-[40rpx]">
            <view
                v-if="isLogin"
                class="flex items-center"
                @click="navigateTo('/pages/user_data/user_data')"
            >
                <u-avatar :src="user.avatar" :size="120"></u-avatar>
                <view class="ml-[20rpx]" :style="{ color: content.text_color || '#ffffff' }">
                    <view
                        v-if="user.account"
                        class="text-lg"
                    >
                        账号：{{ user.account }}
                    </view>
                    <view
                        class="text-xs mt-[10rpx] flex items-center"
                        @click.stop="copy(user.sn || user.id)"
                    >
                        ID：{{ user.sn || user.id }}
                        <u-icon name="copy" class="ml-1" size="24"></u-icon>
                    </view>
                </view>
            </view>
            <navigator v-else class="flex items-center" hover-class="none" url="/pages/login/login">
                <u-avatar src="/static/images/user/default_avatar.png" :size="120"></u-avatar>
                <view
                    class="text-3xl ml-[20rpx]"
                    :style="{ color: content.text_color || '#ffffff' }"
                    >未登录</view
                >
            </navigator>
            <navigator v-if="isLogin" hover-class="none" url="/pages/user_set/user_set">
                <u-icon name="setting" :color="content.text_color || '#ffffff'" :size="48"></u-icon>
            </navigator>
        </view>
    </view>
</template>
<script lang="ts" setup>
import { useCopy } from '@/hooks/useCopy'
import { computed } from 'vue'
import defaultBgImg from '@/static/images/user/my_topbg.png'

const props = defineProps({
    pageMeta: {
        type: Array,
        default: () => []
    },
    content: {
        type: Object,
        default: () => ({})
    },
    styles: {
        type: Object,
        default: () => ({})
    },
    user: {
        type: Object,
        default: () => ({})
    },
    isLogin: {
        type: Boolean
    }
})
const { copy } = useCopy()

const metaData: any = computed(() => {
    const first: any = (props.pageMeta as any[])?.[0]
    return first?.content ?? {}
})

const bgStyle = computed(() => {
    const { background_type, background_image, background_color } = props.content
    if (background_type == 1 && background_image) {
        return {
            backgroundImage: `url(${background_image})`,
            backgroundRepeat: 'no-repeat',
            backgroundSize: 'cover',
            backgroundPosition: 'center'
        }
    } else if (background_type == 2 && background_color) {
        return {
            backgroundColor: `${background_color} !important`,
            backgroundImage: 'none !important' // 清除默认背景图
        }
    }
    // 默认样式
    return {
        backgroundImage: `url(${defaultBgImg}), linear-gradient(90deg, var(--color-primary), var(--color-minor))`,
        backgroundRepeat: 'no-repeat',
        backgroundPosition: 'bottom',
        backgroundSize: '100%'
    }
})

const navigateTo = (url: string) => {
    uni.navigateTo({
        url
    })
}
</script>

<style lang="scss" scoped>
.user-info {
    // 移除默认背景，只保留基本样式
    width: 100%;
    min-height: 220rpx;
    // background-size: 100% 100%; // 移入 JS 控制
}
</style>
