<template>
    <div
        class="my-service mx-[20rpx] mt-[20rpx] rounded-lg p-[30rpx]"
        :style="{ backgroundColor: styles.background_color }"
    >
        <div
            v-if="content.title"
            class="title font-medium text-lg"
            :style="{ color: styles.title_color }"
        >
            <div>{{ content.title }}</div>
        </div>
        <div v-if="content.style == 1" class="grid grid-cols-4 gap-x-9 gap-y-7">
            <div
                v-for="(item, index) in showList"
                :key="index"
                class="flex flex-col items-center pt-[40rpx]"
                @click="handleClick(item.link)"
            >
                <u-image width="52" height="52" :src="getImageUrl(item.image)" alt="" />
                <div class="mt-[22rpx] text-sm" :style="{ color: styles.text_color }">
                    {{ item.name }}
                </div>
            </div>
        </div>
        <div v-if="content.style == 2">
            <div
                v-for="(item, index) in showList"
                :key="index"
                class="flex items-center border-light border-solid border-0 border-b h-[100rpx] px-[24rpx]"
                @click="handleClick(item.link)"
            >
                <u-image width="48" height="48" :src="getImageUrl(item.image)" alt="" />
                <div class="ml-[20rpx] flex-1 text-sm" :style="{ color: styles.text_color }">
                    {{ item.name }}
                </div>
                <div class="text-muted">
                    <u-icon name="arrow-right" />
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import { useAppStore } from '@/stores/app'
import { navigateTo } from '@/utils/util'
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
const { getImageUrl } = useAppStore()
const handleClick = (link: any) => {
    navigateTo(link)
}

const showList = computed(() => {
    return props.content.data?.filter((item: any) => item.is_show == '1') || []
})
</script>

<style lang="scss"></style>
