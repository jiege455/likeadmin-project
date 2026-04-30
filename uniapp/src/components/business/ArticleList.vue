<template>
    <div class="article-list">
        <van-list
            v-model:loading="loading"
            :finished="finished"
            finished-text="没有更多了"
            @load="onLoad"
        >
            <van-cell
                v-for="item in list"
                :key="item.id"
                :title="item.title"
                :label="item.summary"
                is-link
                @click="onClick(item)"
            >
                <template #icon>
                    <van-image
                        width="60"
                        height="60"
                        :src="item.image"
                        fit="cover"
                        style="margin-right: 10px"
                    />
                </template>
                <template #value>
                    <span style="font-size: 12px; color: #999">{{ item.create_time }}</span>
                </template>
            </van-cell>
        </van-list>
    </div>
</template>

<script setup lang="ts">
/**
 * ArticleList 文章列表展示
 *
 * Props:
 * - list: 文章数组
 * - loading: 是否加载中
 * - finished: 是否加载完成
 *
 * Events:
 * - click-item: 点击文章时触发
 * - load: 滚动到底部加载更多
 */
import { toRefs } from 'vue'

interface Article {
    id: number
    title: string
    summary: string
    image: string
    create_time: string
    [key: string]: any
}

const props = defineProps<{
    list: Article[]
    loading: boolean
    finished?: boolean
}>()

const emit = defineEmits<{
    (e: 'click-item', item: Article): void
    (e: 'update:loading', loading: boolean): void
    (e: 'load'): void
}>()

const { loading } = toRefs(props)

const onLoad = () => {
    emit('load')
}

const onClick = (item: Article) => {
    emit('click-item', item)
}
</script>

<style scoped>
.article-list {
    background-color: #f7f8fa;
}
</style>
