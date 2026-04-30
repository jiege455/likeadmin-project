<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
  功能说明：通用悬浮按钮组件，可自定义样式和跳转
-->
<template>
    <view
        class="w-float-btn"
        :style="{
            marginTop: `${styles.margin_top || 0}px`,
            marginBottom: `${styles.margin_bottom || 10}px`,
            marginLeft: `${styles.padding_horizontal || 12}px`,
            marginRight: `${styles.padding_horizontal || 12}px`
        }"
    >
        <view
            class="btn-card flex items-center justify-between px-4 py-3 rounded-xl"
            :style="{
                backgroundColor: content.bg_color || '#EF4444',
                borderRadius: `${styles.border_radius || 12}px`
            }"
            @click="handleClick"
        >
            <view class="flex items-center">
                <view
                    class="icon-box w-10 h-10 rounded-full flex items-center justify-center"
                    :style="{ backgroundColor: 'rgba(255,255,255,0.2)' }"
                >
                    <u-icon
                        :name="content.icon || 'star'"
                        size="20"
                        :color="content.icon_color || '#ffffff'"
                    ></u-icon>
                </view>
                <view class="ml-3">
                    <view
                        class="text-base font-bold"
                        :style="{ color: content.title_color || '#ffffff' }"
                    >
                        {{ content.title || '我的关注' }}
                    </view>
                    <view
                        v-if="content.subtitle"
                        class="text-xs mt-1"
                        :style="{ color: content.subtitle_color || 'rgba(255,255,255,0.8)' }"
                    >
                        {{ content.subtitle }}
                    </view>
                </view>
            </view>
            <view
                class="action-btn px-4 py-2 rounded-full text-sm font-medium"
                :style="{
                    backgroundColor: content.btn_bg_color || '#ffffff',
                    color: content.btn_color || '#EF4444'
                }"
            >
                {{ content.btn_text || '去查看' }}
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
const props = defineProps({
    content: {
        type: Object,
        default: () => ({})
    },
    styles: {
        type: Object,
        default: () => ({})
    }
})

const handleClick = () => {
    const link = props.content.link
    if (!link) return

    if (typeof link === 'string') {
        uni.navigateTo({ url: link })
    } else if (link.path) {
        uni.navigateTo({ url: link.path })
    }
}
</script>

<style lang="scss" scoped>
.w-float-btn {
    .btn-card {
        box-shadow: 0 4rpx 12rpx rgba(0, 0, 0, 0.1);
        transition: transform 0.15s ease;

        &:active {
            transform: scale(0.98);
        }
    }

    .action-btn {
        transition: transform 0.15s ease;

        &:active {
            transform: scale(0.95);
        }
    }
}
</style>
