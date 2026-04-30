<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
  功能说明：悬浮按钮组件属性设置
-->
<template>
    <div class="float-btn-setting">
        <el-form label-width="80px">
            <div class="divider">内容设置</div>
            
            <el-form-item label="标题">
                <el-input :model-value="content.title" @change="updateContent('title', $event)" placeholder="我的关注" />
            </el-form-item>
            
            <el-form-item label="副标题">
                <el-input :model-value="content.subtitle" @change="updateContent('subtitle', $event)" placeholder="查看关注的商家" />
            </el-form-item>
            
            <el-form-item label="按钮文字">
                <el-input :model-value="content.btn_text" @change="updateContent('btn_text', $event)" placeholder="去查看" />
            </el-form-item>
            
            <el-form-item label="跳转链接">
                <link-picker :model-value="content.link" @change="updateContent('link', $event)" />
            </el-form-item>
            
            <div class="divider">颜色设置</div>
            
            <el-form-item label="背景颜色">
                <color-picker :model-value="content.bg_color" @change="updateContent('bg_color', $event)" reset-color="#EF4444" />
            </el-form-item>
            
            <el-form-item label="标题颜色">
                <color-picker :model-value="content.title_color" @change="updateContent('title_color', $event)" reset-color="#ffffff" />
            </el-form-item>
            
            <el-form-item label="副标题颜色">
                <color-picker :model-value="content.subtitle_color" @change="updateContent('subtitle_color', $event)" reset-color="rgba(255,255,255,0.8)" />
            </el-form-item>
            
            <el-form-item label="图标颜色">
                <color-picker :model-value="content.icon_color" @change="updateContent('icon_color', $event)" reset-color="#ffffff" />
            </el-form-item>
            
            <el-form-item label="按钮背景">
                <color-picker :model-value="content.btn_bg_color" @change="updateContent('btn_bg_color', $event)" reset-color="#ffffff" />
            </el-form-item>
            
            <el-form-item label="按钮文字">
                <color-picker :model-value="content.btn_color" @change="updateContent('btn_color', $event)" reset-color="#EF4444" />
            </el-form-item>
            
            <div class="divider">样式设置</div>
            
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
        </el-form>
        
        <div class="tips-box">
            <el-alert
                title="组件说明"
                type="info"
                :closable="false"
            >
                <template #default>
                    <p>这是一个通用的悬浮按钮组件，可以自定义：</p>
                    <ul class="tips-list">
                        <li>标题、副标题、按钮文字</li>
                        <li>背景色、文字颜色、按钮颜色</li>
                        <li>跳转链接（支持页面跳转）</li>
                    </ul>
                </template>
            </el-alert>
        </div>
    </div>
</template>

<script lang="ts" setup>
import LinkPicker from '@/components/link/picker.vue'
import ColorPicker from '@/components/color-picker/index.vue'

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

// 更新 content 的某个字段
const updateContent = (key, value) => {
    emit('update:content', { ...props.content, [key]: value })
}

// 更新 styles 的某个字段
const updateStyles = (key, value) => {
    emit('update:styles', { ...props.styles, [key]: value })
}
</script>

<style lang="scss" scoped>
.divider {
    @apply bg-gray-100 py-2 px-4 mb-4 text-sm font-bold text-gray-600;
}

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
