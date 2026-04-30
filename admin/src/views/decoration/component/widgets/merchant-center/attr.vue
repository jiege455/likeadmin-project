<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
  功能说明：商家中心入口组件属性设置
-->
<template>
    <div class="merchant-center-setting">
        <el-form label-width="80px">
            <el-card shadow="never" class="!border-none mb-3">
                <template #header>
                    <div class="text-base font-medium">样式设置</div>
                </template>
                
                <el-form-item label="主题色">
                    <el-color-picker :model-value="styles.primary_color" @change="updateStyles('primary_color', $event)" />
                    <div class="text-xs text-gray-400 mt-1">头部背景、图标颜色</div>
                </el-form-item>
                
                <el-form-item label="上边距">
                    <el-slider :model-value="styles.margin_top" @change="updateStyles('margin_top', $event)" :min="0" :max="50" show-input />
                </el-form-item>
                
                <el-form-item label="下边距">
                    <el-slider :model-value="styles.margin_bottom" @change="updateStyles('margin_bottom', $event)" :min="0" :max="50" show-input />
                </el-form-item>
                
                <el-form-item label="左右边距">
                    <el-slider :model-value="styles.padding_horizontal" @change="updateStyles('padding_horizontal', $event)" :min="0" :max="30" show-input />
                </el-form-item>
                
                <el-form-item label="圆角">
                    <el-slider :model-value="styles.border_radius" @change="updateStyles('border_radius', $event)" :min="0" :max="30" show-input />
                </el-form-item>
            </el-card>
        </el-form>
        
        <div class="tips-box">
            <el-alert
                title="组件说明"
                type="info"
                :closable="false"
            >
                <template #default>
                    <p>此组件会根据用户的商家状态自动显示不同内容：</p>
                    <ul class="tips-list">
                        <li>未登录：显示登录提示</li>
                        <li>未申请：显示"成为商家"引导</li>
                        <li>审核中：显示审核状态</li>
                        <li>已通过：显示商家中心入口和数据</li>
                        <li>已拒绝：显示重新申请入口</li>
                    </ul>
                </template>
            </el-alert>
        </div>
    </div>
</template>

<script lang="ts" setup>
const emit = defineEmits<{
    (event: 'update:styles', data: any): void
    (event: 'update:content', data: any): void
}>()

const props = defineProps({
    content: {
        type: Object,
        default: () => ({})
    },
    styles: {
        type: Object,
        default: () => ({
            margin_top: 0,
            margin_bottom: 10,
            padding_horizontal: 12,
            border_radius: 12,
            primary_color: '#EF4444'
        })
    }
})

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

<style lang="scss" scoped>
.tips-box {
    margin-top: 20px;
}

.tips-list {
    margin: 8px 0 0 0;
    padding-left: 20px;
    
    li {
        margin: 4px 0;
        font-size: 12px;
        color: #666;
    }
}
</style>
