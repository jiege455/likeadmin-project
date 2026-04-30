<!--
  PC Banner 组件
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
-->
<template>
    <view class="pc-banner" v-if="bannerList.length > 0">
        <swiper
            class="banner-swiper"
            :autoplay="true"
            :interval="3000"
            :circular="true"
            :indicator-dots="bannerList.length > 1"
            indicator-color="rgba(255,255,255,0.5)"
            indicator-active-color="#fff"
        >
            <swiper-item v-for="(item, index) in bannerList" :key="index">
                <image
                    v-if="item.image"
                    :src="item.image"
                    mode="widthFix"
                    class="w-full"
                    @click="handleClick(item)"
                />
            </swiper-item>
        </swiper>
    </view>
</template>

<script setup lang="ts">
import { computed } from 'vue'

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

const bannerList = computed(() => {
    if (!props.content?.enabled) return []
    const data = props.content?.data || []
    return data.filter((item: any) => item.image)
})

const handleClick = (item: any) => {
    if (item.link && Object.keys(item.link).length > 0) {
        const link = item.link
        if (link.path) {
            uni.navigateTo({
                url: link.path
            })
        }
    }
}
</script>

<style lang="scss" scoped>
.pc-banner {
    width: 100%;

    .banner-swiper {
        width: 100%;
        height: auto;
    }

    image {
        display: block;
        width: 100%;
    }
}
</style>
