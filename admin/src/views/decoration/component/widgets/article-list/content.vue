<template>
    <div class="article-list-content">
        <div class="header" v-if="content.title">
            <div class="title">{{ content.title }}</div>
            <div class="more">更多 ></div>
        </div>
        <div class="list-item" v-for="i in 3" :key="i">
            <div class="info">
                <div class="title line-2">示例文章标题展示，这里是文章标题...</div>
                <div class="bottom">
                    <div class="price" v-if="content.showPrice">¥ 9.99</div>
                    <div class="time">2023-01-01</div>
                </div>
            </div>
            <div class="image">
                <img src="@/assets/images/default_image.png" alt="" />
            </div>
        </div>
    </div>
</template>

<script lang="ts" setup>
import type { PropType } from 'vue'
import type { optionsType } from './options'

defineProps({
    content: {
        type: Object as PropType<optionsType>,
        default: () => ({})
    },
    styles: {
        type: Object as PropType<any>,
        default: () => ({})
    }
})
</script>

<style lang="scss" scoped>
.article-list-content {
    background-color: #fff;
    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 10px 0;
        .title {
            font-size: 16px;
            font-weight: bold;
        }
        .more {
            font-size: 12px;
            color: #999;
        }
    }
    .list-item {
        display: flex;
        padding: 10px;
        border-bottom: 1px solid #f2f2f2;
        .info {
            flex: 1;
            margin-right: 10px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            .title {
                font-size: 14px;
                color: #333;
                margin-bottom: 5px;
            }
            .bottom {
                display: flex;
                justify-content: space-between;
                align-items: center;
                .price {
                    color: #ff5500;
                    font-size: 14px;
                    font-weight: bold;
                }
                .time {
                    font-size: 12px;
                    color: #999;
                }
            }
        }
        .image {
            width: 80px;
            height: 60px;
            background-color: #f2f2f2;
            img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                border-radius: 4px;
            }
        }
    }
}
</style>
