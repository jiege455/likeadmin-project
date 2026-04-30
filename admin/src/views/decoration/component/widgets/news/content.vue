<template>
    <div class="news">
        <div class="flex items-center news-title mx-[10px] my-[15px] text-[17px] font-medium">
            {{ content.title || '最新资料' }}
        </div>
        <div
            v-for="(item, index) in previewList"
            :key="index"
            class="news-card flex bg-white px-[10px] py-[16px] text-[#333] border-[#f2f2f2] border-b"
        >
            <div class="mr-[10px]" v-if="item.image">
                <img :src="item.image" class="w-[120px] h-[90px] object-contain" />
            </div>
            <div class="flex flex-col justify-between flex-1">
                <div class="text-[15px] font-medium line-clamp-2">{{ item.title }}</div>
                <div class="line-clamp-1 text-sm mt-[8px]">
                    {{ item.desc }}
                </div>
                <div class="text-[#999] text-xs w-full flex justify-between mt-[8px]">
                    <div>{{ item.create_time }}</div>
                    <div class="flex items-center">
                        <icon name="el-icon-View" />
                        <div class="ml-[5px]">{{ item.click }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import type { PropType } from 'vue'

import type options from './options'

type OptionsType = ReturnType<typeof options>
defineProps({
    content: {
        type: Object as PropType<OptionsType['content']>,
        default: () => ({})
    },
    styles: {
        type: Object as PropType<OptionsType['styles']>,
        default: () => ({})
    }
})

const previewList = [
    {
        image: '',
        title: '文章标题占位符',
        desc: '文章摘要内容占位符，用于展示文章简介...',
        create_time: '2023-01-01',
        click: 999
    },
    {
        image: '',
        title: '文章标题占位符2',
        desc: '文章摘要内容占位符，用于展示文章简介...',
        create_time: '2023-01-02',
        click: 888
    }
]
</script>

<style lang="scss" scoped>
.news {
    .news-title {
        &::before {
            content: '';
            width: 4px;
            height: 17px;
            display: block;
            margin-right: 5px;
            background: #4173ff;
        }
    }
}
</style>
