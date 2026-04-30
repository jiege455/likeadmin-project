<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
-->
<template>
    <el-form label-width="70px">
        <el-card shadow="never" class="!border-none flex mt-2">
            <el-form-item label="布局样式">
                <el-radio-group :model-value="content.style" @change="updateContent('style', $event)">
                    <el-radio :label="1">一行两个</el-radio>
                    <el-radio :label="2">一行三个</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item label="背景颜色">
                <color-picker :model-value="styles.bg_color" @change="updateStyles('bg_color', $event)" />
            </el-form-item>
             <el-form-item label="外边距">
                <el-slider :model-value="styles.margin" @change="updateStyles('margin', $event)" :max="20" />
            </el-form-item>
            <el-form-item label="内边距">
                <el-slider :model-value="styles.padding" @change="updateStyles('padding', $event)" :max="20" />
            </el-form-item>
             <el-form-item label="圆角">
                <el-slider :model-value="styles.radius" @change="updateStyles('radius', $event)" :max="20" />
            </el-form-item>
        </el-card>

        <el-card shadow="never" class="!border-none flex mt-2">
            <div class="text-base font-medium mb-4">图片设置</div>
            <div class="alert alert-info mb-4">
                建议尺寸：{{ content.style === 1 ? '340px * 340px' : '220px * 220px' }}
            </div>
            
            <draggable
                class="draggable"
                v-model="dataList"
                animation="300"
                handle=".drag-move"
                item-key="index"
            >
                <template v-slot:item="{ element: item, index }">
                    <del-wrap :key="index" @close="handleDelete(index)" class="w-full">
                        <div class="bg-fill-light w-full p-4 mt-4">
                            <div class="flex">
                                <material-picker
                                    v-model="item.image"
                                    upload-class="bg-body"
                                    exclude-domain
                                />
                                <div class="flex-1 ml-4">
                                    <el-form-item label="链接">
                                        <link-picker v-model="item.link" />
                                    </el-form-item>
                                    <div class="drag-move cursor-move ml-auto text-right mt-2">
                                        <icon name="el-icon-Rank" size="18" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </del-wrap>
                </template>
            </draggable>
            
            <div class="mt-4">
                <el-button class="w-full" type="primary" @click="handleAdd">添加图片</el-button>
            </div>
        </el-card>
    </el-form>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import type { PropType } from 'vue'
import ColorPicker from '@/components/color-picker/index.vue'
import Draggable from 'vuedraggable'
import feedback from '@/utils/feedback'
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

const emit = defineEmits(['update:content', 'update:styles'])

const updateContent = (key: string, value: any) => {
    emit('update:content', { ...props.content, [key]: value })
}

const updateStyles = (key: string, value: any) => {
    emit('update:styles', { ...props.styles, [key]: value })
}

const dataList = computed({
    get: () => props.content.data || [],
    set: (val) => {
        emit('update:content', { ...props.content, data: val })
    }
})

const handleAdd = () => {
    const newData = [...(props.content.data || [])]
    newData.push({
        image: '',
        link: {
            path: '',
            name: '',
            type: '',
            query: {}
        }
    })
    emit('update:content', { ...props.content, data: newData })
}

const handleDelete = (index: number) => {
    const data = props.content.data || []
    if (data.length <= 1) {
        return feedback.msgError('至少保留一张图片')
    }
    const newData = [...data]
    newData.splice(index, 1)
    emit('update:content', { ...props.content, data: newData })
}
</script>

<style lang="scss" scoped>
.alert {
    padding: 8px 16px;
    background-color: #e6f7ff;
    border: 1px solid #91d5ff;
    border-radius: 4px;
    font-size: 12px;
    color: #1890ff;
}
</style>
