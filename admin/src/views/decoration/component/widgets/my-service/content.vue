<template>
    <div class="my-service" :style="{ backgroundColor: styles.background_color }">
        <div v-if="content.title" class="title px-[15px] py-[10px]" :style="{ color: styles.title_color }">
            <div>{{ content.title }}</div>
        </div>
        <div v-if="content.style == 1" class="flex flex-wrap pt-[20px] pb-[10px]">
            <div
                v-for="(item, index) in showList"
                :key="index"
                class="flex flex-col items-center w-1/4 mb-[15px]"
            >
                <decoration-img width="26px" height="26px" :src="item.image" alt="" />
                <div class="mt-[7px]" :style="{ color: styles.text_color }">{{ item.name }}</div>
            </div>
        </div>
        <div v-if="content.style == 2">
            <div
                v-for="(item, index) in showList"
                :key="index"
                class="flex items-center border-b border-[#e5e5e5] h-[50px] px-[12px]"
            >
                <decoration-img width="24px" height="24px" :src="item.image" alt="" />
                <div class="ml-[10px] flex-1" :style="{ color: styles.text_color }">{{ item.name }}</div>
                <div>
                    <icon name="el-icon-ArrowRight" />
                </div>
            </div>
        </div>
    </div>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'

import DecorationImg from '../../decoration-img.vue'
import type options from './options'

type OptionsType = ReturnType<typeof options>
const props = defineProps({
    content: {
        type: Object as PropType<OptionsType['content']>,
        default: () => ({})
    },
    styles: {
        type: Object as PropType<OptionsType['styles']>,
        default: () => ({})
    }
})
const showList = computed(() => {
    return props.content.data?.filter((item: any) => item.is_show == '1') || []
})
</script>

<style lang="scss" scoped>
.my-service {
    margin: 10px 10px 0;
    // background-color: #fff; // 移除默认白色背景，改由内联样式控制
    border-radius: 7px;
    .title {
        border-bottom: 1px solid #e5e5e5;
        font-size: 16px;
        font-weight: 500;
    }
}
</style>
