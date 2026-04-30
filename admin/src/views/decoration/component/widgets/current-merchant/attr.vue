<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
-->
<template>
    <div class="current-merchant-attr">
        <el-collapse v-model="activeNames">
            <el-collapse-item title="功能设置" name="function">
                <el-form label-width="90px">
                    <el-form-item label="预览状态">
                        <el-radio-group :model-value="content.show_has_merchant" @change="updateContent('show_has_merchant', $event)">
                            <el-radio :label="false">无商家状态</el-radio>
                            <el-radio :label="true">有商家状态</el-radio>
                        </el-radio-group>
                    </el-form-item>
                    <el-form-item label="显示切换按钮">
                        <el-switch :model-value="content.show_switch" @change="updateContent('show_switch', $event)" />
                    </el-form-item>
                    <el-form-item label="显示操作按钮">
                        <el-switch :model-value="content.show_actions" @change="updateContent('show_actions', $event)" />
                        <div class="form-tip">包括：商家微信、推广TA、私聊、投诉反馈</div>
                    </el-form-item>
                    <el-form-item label="显示Tab栏">
                        <el-switch :model-value="content.show_tabs" @change="updateContent('show_tabs', $event)" />
                        <div class="form-tip">包括：TA的料、优惠券</div>
                    </el-form-item>
                    <el-form-item label="显示搜索栏">
                        <el-switch :model-value="content.show_search" @change="updateContent('show_search', $event)" />
                    </el-form-item>
                    <el-form-item label="显示资料列表">
                        <el-switch :model-value="content.show_content_list" @change="updateContent('show_content_list', $event)" />
                    </el-form-item>
                    <el-form-item label="显示优惠券">
                        <el-switch :model-value="content.show_coupon" @change="updateContent('show_coupon', $event)" />
                    </el-form-item>
                    <el-form-item label="显示申请按钮">
                        <el-switch :model-value="content.show_apply_btn" @change="updateContent('show_apply_btn', $event)" />
                        <div class="form-tip">无商家状态下显示"申请做商家"按钮</div>
                    </el-form-item>
                </el-form>
            </el-collapse-item>
            
            <el-collapse-item title="颜色设置" name="color">
                <el-form label-width="90px">
                    <el-form-item label="主题色">
                        <el-color-picker :model-value="styles.primary_color" @change="updateStyles('primary_color', $event)" />
                        <div class="form-tip">头部背景、按钮等主题颜色</div>
                    </el-form-item>
                    <el-form-item label="标题颜色">
                        <el-color-picker :model-value="styles.title_color" @change="updateStyles('title_color', $event)" />
                    </el-form-item>
                    <el-form-item label="简介颜色">
                        <el-color-picker :model-value="styles.desc_color" @change="updateStyles('desc_color', $event)" />
                    </el-form-item>
                </el-form>
            </el-collapse-item>
            
            <el-collapse-item title="边距设置" name="margin">
                <el-form label-width="90px">
                    <el-form-item label="上边距">
                        <el-input-number :model-value="styles.margin_top" @change="updateStyles('margin_top', $event)" :min="0" :max="100" />
                        <span class="unit">px</span>
                    </el-form-item>
                    <el-form-item label="下边距">
                        <el-input-number :model-value="styles.margin_bottom" @change="updateStyles('margin_bottom', $event)" :min="0" :max="100" />
                        <span class="unit">px</span>
                    </el-form-item>
                    <el-form-item label="左右边距">
                        <el-input-number :model-value="styles.padding_horizontal" @change="updateStyles('padding_horizontal', $event)" :min="0" :max="50" />
                        <span class="unit">px</span>
                    </el-form-item>
                    <el-form-item label="圆角大小">
                        <el-input-number :model-value="styles.border_radius" @change="updateStyles('border_radius', $event)" :min="0" :max="50" />
                        <span class="unit">px</span>
                    </el-form-item>
                </el-form>
            </el-collapse-item>
        </el-collapse>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue'

const props = defineProps({
    content: {
        type: Object,
        default: () => ({
            show_switch: true,
            show_actions: true,
            show_tabs: true,
            show_search: true,
            show_content_list: true,
            show_coupon: true,
            show_apply_btn: true,
            show_has_merchant: true
        })
    },
    styles: {
        type: Object,
        default: () => ({
            primary_color: '#FF2D3A',
            title_color: '#333',
            desc_color: '#666',
            margin_top: 0,
            margin_bottom: 10,
            padding_horizontal: 12,
            border_radius: 12
        })
    }
})

const emit = defineEmits(['update:content', 'update:styles'])

const activeNames = ref(['function', 'color', 'margin'])

// 更新 content 的某个字段
const updateContent = (key: string, value: any) => {
    emit('update:content', {
        ...props.content,
        [key]: value
    })
}

// 更新 styles 的某个字段
const updateStyles = (key: string, value: any) => {
    emit('update:styles', {
        ...props.styles,
        [key]: value
    })
}
</script>

<style scoped>
.current-merchant-attr {
    padding: 10px;
}

.form-tip {
    font-size: 12px;
    color: #999;
    margin-top: 4px;
}

.unit {
    margin-left: 8px;
    color: #666;
    font-size: 12px;
}

:deep(.el-collapse-item__header) {
    font-weight: bold;
    font-size: 14px;
}

:deep(.el-form-item) {
    margin-bottom: 16px;
}

:deep(.el-form-item__label) {
    font-size: 13px;
    color: #666;
}
</style>
