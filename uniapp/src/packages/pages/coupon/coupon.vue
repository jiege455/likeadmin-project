<template>
    <page-meta :page-style="$theme.pageStyle">
        <!-- #ifndef H5 -->
        <navigation-bar
            :front-color="$theme.navColor"
            :background-color="$theme.navBgColor"
            :title="pageTitle"
            :color="$theme.navColor"
        />
        <!-- #endif -->
    </page-meta>
    <view class="coupon-page min-h-screen bg-[#f5f5f5]">
        <!-- 装修组件渲染 -->
        <block v-for="(item, index) in pageData" :key="index">
            <w-custom-navbar
                v-if="item.name === 'custom-navbar'"
                :content="item.content"
                :styles="item.styles"
            />
            <w-coupon-center
                v-if="item.name === 'coupon-center'"
                :content="item.content"
                :styles="item.styles"
            />
            <!-- 其他通用组件 -->
            <w-banner v-if="item.name === 'banner'" :content="item.content" :styles="item.styles" />
        </block>

        <!-- 加载中 -->
        <view
            v-if="pageData.length === 0"
            class="flex flex-col items-center justify-center min-h-[80vh]"
        >
            <u-loading-icon mode="circle" size="30"></u-loading-icon>
            <view class="mt-2 text-gray-400">加载中...</view>
        </view>
    </view>
</template>

<script lang="ts" setup>
import { ref, onMounted } from 'vue'
import { getDecorate } from '@/api/shop'
import { onShow } from '@dcloudio/uni-app'
import wCustomNavbar from '@/components/widgets/w-custom-navbar/w-custom-navbar.vue'
import wCouponCenter from '@/components/widgets/w-coupon-center/w-coupon-center.vue'
// 假设已有 w-banner 组件，如没有需引入或忽略
// import wBanner from '@/components/widgets/w-banner/w-banner.vue'

const pageData = ref<any[]>([])
const pageTitle = ref('领券中心')

const getPageData = async () => {
    try {
        // ID 9 是我们在后端定义的“领券中心”页面ID
        const res = await getDecorate({ id: 9 })
        if (res.data) {
            pageData.value = JSON.parse(res.data)
            // 尝试从自定义导航栏中获取标题
            const navItem = pageData.value.find((item: any) => item.name === 'custom-navbar')
            if (navItem && navItem.content.title) {
                pageTitle.value = navItem.content.title
                uni.setNavigationBarTitle({ title: navItem.content.title })
            }
        } else {
            // 如果没有装修数据，可以使用默认配置（可选）
            pageData.value = [
                {
                    name: 'coupon-center',
                    content: { show_my_coupon: 1 },
                    styles: {}
                }
            ]
        }
    } catch (e) {
        console.error(e)
        // 异常兜底
        pageData.value = [
            {
                name: 'coupon-center',
                content: { show_my_coupon: 1 },
                styles: {}
            }
        ]
    }
}

onShow(() => {
    getPageData()
})
</script>

<style lang="scss" scoped>
.coupon-page {
    /* min-height: 100vh; */
}
</style>
