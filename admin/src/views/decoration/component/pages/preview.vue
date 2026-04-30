<template>
    <el-scrollbar class="pages-preview-container">
        <div class="shadow mx-[30px] pages-preview" :style="pageStyle">
            <!-- 页面设置组件 (PageMeta) -->
            <div
                v-if="pageMeta && pageMeta[0]"
                class="relative cursor-pointer hover:opacity-90 transition-opacity"
                @click.stop="handleClickPageMeta"
            >
                <div
                    class="absolute w-full h-full z-[100] border-dashed pointer-events-none"
                    :class="{
                        select: modelValue === -1
                    }"
                ></div>
                <component
                    :is="widgets['page-meta']?.content"
                    :content="pageMeta[0].content"
                    :styles="pageMeta[0].styles"
                />
            </div>

            <div
                v-for="(widget, index) in pageData"
                :key="widget.id"
                class="relative"
                :class="{
                    'cursor-pointer': !widget?.disabled
                }"
                @click.stop="handleClick(widget, index)"
            >
                <!--  选中的边框  -->
                <div
                    class="absolute w-full h-full z-[100] border-dashed"
                    :class="{
                        select: index == modelValue,
                        hide: canShowCom(widget.content),
                        'border-[#dcdfe6] border-2': !widget?.disabled
                    }"
                ></div>
                <!--  选中的组件  -->
                <slot>
                    <div v-if="!widgets[widget?.name]" class="p-4 text-center bg-red-50 border border-red-200 m-2 rounded">
                        <p class="text-red-500 font-bold mb-1">⚠️ 组件加载失败</p>
                        <p class="text-xs text-gray-500">组件名: {{ widget?.name }}</p>
                        <p class="text-xs text-gray-500 mt-1">请检查文件命名是否为小写且一致</p>
                    </div>
                    <component
                        v-else
                        :is="widgets[widget?.name]?.content"
                        :content="widget.content"
                        :styles="widget.styles"
                        :key="widget.id"
                        class="relative"
                    />
                </slot>
                <!--  部件操作按钮组  -->
                <div class="widget-btns py-[5px]" v-if="index == modelValue">
                    <div>
                        <el-tooltip
                            effect="dark"
                            :content="canShowCom(widget.content) ? '显示' : '隐藏'"
                            placement="right"
                        >
                            <el-button
                                class="py-[5px]"
                                type="primary"
                                :icon="canShowCom(widget.content) ? View : Hide"
                                @click="changeShowCom(widget.content, index)"
                            />
                        </el-tooltip>
                    </div>
                    <div>
                        <el-tooltip effect="dark" content="上移" placement="right">
                            <el-button
                                class="py-[5px]"
                                type="primary"
                                :icon="ArrowUpBold"
                                :disabled="canMoveUpCom(index)"
                                @click.stop="rearrangeArray(index, index - 1)"
                            />
                        </el-tooltip>
                    </div>
                    <div>
                        <el-tooltip effect="dark" content="删除" placement="right">
                            <el-button
                                class="py-[5px]"
                                type="primary"
                                :icon="Delete"
                                @click.stop="handleDelete(index)"
                            />
                        </el-tooltip>
                    </div>
                </div>
            </div>
        </div>
    </el-scrollbar>
</template>
<script lang="ts" setup>
import { ArrowDownBold, ArrowUpBold, Hide, View, Delete } from '@element-plus/icons-vue'
import { cloneDeep } from 'lodash-es'
import type { PropType } from 'vue'
import { computed, ref, onMounted, provide } from 'vue'
import { getDecoratePages } from '@/api/decoration'

import widgets from '../widgets'

const props = defineProps({
    pageMeta: {
        type: Object as any,
        default: () => null
    },
    pageData: {
        type: Array as PropType<any[]>,
        default: () => []
    },
    modelValue: {
        type: Number,
        default: 0
    }
})

const emit = defineEmits<{
    (event: 'update:modelValue', value: number): void
    (event: 'updatePageData', value: any[]): void
}>()

const themeColor = ref('#2F80ED') // 默认蓝色，与系统风格主题一致

const pageStyle = computed(() => {
    const meta = props.pageMeta?.[0]?.content || {}
    const style: Record<string, string> = {
        backgroundColor: meta.bg_color || '#f8f8f8',
        backgroundRepeat: 'no-repeat',
        backgroundSize: '100% auto',
        '--color-primary': themeColor.value // 注入主题色变量
    }
    
    if (meta.bg_type == 2 && meta.bg_image) {
        style.backgroundImage = `url(${meta.bg_image})`
    }
    
    return style
})

// 获取系统主题色
const getSystemTheme = async () => {
    try {
        const res = await getDecoratePages({ id: 5 })
        // res 已经是 { id: 5, data: "{...}" } 格式
        if (res && res.data) {
            const data = JSON.parse(res.data)
            themeColor.value = data.themeColor1 || '#2F80ED' // 默认为蓝色
        }
    } catch (e) {
        console.error('获取系统主题失败:', e)
    }
}

onMounted(() => {
    getSystemTheme()
})

// 提供系统主题色给子组件
provide('themeColor', themeColor)

const oldModelValue = ref<number>(-1)

const handleClickPageMeta = () => {
    if (props.modelValue === -1) {
        emit('update:modelValue', oldModelValue.value)
    } else {
        oldModelValue.value = props.modelValue
        emit('update:modelValue', -1)
    }
}

const handleClick = (widget: any, index: number) => {
    // if (widget.disabled) return
    emit('update:modelValue', index)
}

// 是否可以移动组件
const canMoveUpCom = computed(() => {
    return (index: number) => {
        return index === 0
    }
})

// 是否可以移动组件
const canMoveDownCom = computed(() => {
    return (index: number) => {
        return props.pageData?.length === index + 1
    }
})

// 是否显示组件
const canShowCom = computed(() => {
    return (data: any) => {
        return data?.enabled == 0
    }
})

// 修改组件显示/隐藏
const changeShowCom = (data: any, index: number) => {
    if (data.enabled === undefined) return
    data.enabled = data.enabled ? 0 : 1
    
    // 更新父组件数据
    const newPageData = cloneDeep(props.pageData)
    newPageData[index].content = data
    emit('updatePageData', newPageData)
}

// 删除组件
const handleDelete = (index: number) => {
    const newPageData = cloneDeep(props.pageData)
    newPageData.splice(index, 1)
    emit('updatePageData', newPageData)
    emit('update:modelValue', -1)
}

const rearrangeArray = (currentIdx: number, targetIdx: number) => {
    if (
        currentIdx < 0 ||
        currentIdx >= props.pageData.length ||
        targetIdx < 0 ||
        targetIdx >= props.pageData.length
    ) {
        return
    }

    // const element = props.pageData.splice(currentIdx, 1)[0]
    // props.pageData.splice(targetIdx, 0, element)
    const newPageData = cloneDeep(props.pageData)
    const element = newPageData.splice(currentIdx, 1)[0]
    newPageData.splice(targetIdx, 0, element)

    emit('updatePageData', newPageData)
    emit('update:modelValue', targetIdx)
}
</script>

<style lang="scss" scoped>
.pages-preview-container {
    position: relative;
    :deep(.el-scrollbar__wrap) {
        width: 100%;
        display: flex;
        justify-content: center;
    }

    .pages-preview {
        background-color: #f8f8f8;
        width: 360px;
        min-height: 615px;
        color: #333;

        .select {
            @apply border-primary border-solid;
        }

        .hide::before {
            content: '已隐藏';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 14px;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .widget-btns {
            position: absolute;
            top: 10px;
            right: -60px;
            overflow: hidden;

            width: 46px;
            border-radius: 8px;
            @apply bg-primary;

            :deep(.el-button) {
                width: 46px;
                border-radius: 0;
            }
        }
    }
}
</style>
