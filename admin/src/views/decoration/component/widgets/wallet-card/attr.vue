<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
-->
<template>
    <div>
        <el-form label-width="80px">
            <el-form-item label="背景颜色">
                <color-picker :model-value="styles.root_bg_color" @change="updateStyles('root_bg_color', $event)" />
            </el-form-item>
            <el-form-item label="数字颜色">
                <div class="flex items-center">
                    <color-picker :model-value="styles.balance_color" @change="updateStyles('balance_color', $event)" />
                    <el-button link type="primary" class="ml-2" @click="updateStyles('balance_color', '')">重置</el-button>
                </div>
                <div class="form-tips">重置后将自动跟随系统主题色</div>
            </el-form-item>
            <el-form-item label="标签颜色">
                <color-picker :model-value="styles.label_color" @change="updateStyles('label_color', $event)" />
            </el-form-item>
            <el-form-item label="圆角设置">
                <el-slider :model-value="styles.border_radius" @change="updateStyles('border_radius', $event)" :min="0" :max="20" />
            </el-form-item>
            <el-form-item label="上边距">
                <el-slider :model-value="styles.margin_top" @change="updateStyles('margin_top', $event)" :min="0" :max="50" />
            </el-form-item>
            <el-form-item label="下边距">
                <el-slider :model-value="styles.margin_bottom" @change="updateStyles('margin_bottom', $event)" :min="0" :max="50" />
            </el-form-item>
            <el-divider>链接设置</el-divider>
            <el-form-item label="钱包链接">
                <link-picker :model-value="content.wallet_link || defaultWalletLink" @update:model-value="updateContent('wallet_link', $event)" />
            </el-form-item>
            <el-form-item label="佣金链接">
                <link-picker :model-value="content.commission_link || defaultCommissionLink" @update:model-value="updateContent('commission_link', $event)" />
            </el-form-item>
        </el-form>
    </div>
</template>

<script lang="ts" setup>
import type { PropType } from 'vue'
import type options from './options'
import ColorPicker from '@/components/color-picker/index.vue'
import LinkPicker from '@/components/link/picker.vue'

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

const defaultWalletLink = {
    path: '/packages/pages/user_wallet/user_wallet',
    name: '我的钱包',
    type: 'shop'
}

const defaultCommissionLink = {
    path: '/packages/pages/distribution/distribution',
    name: '分销中心',
    type: 'shop'
}

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

<style lang="scss" scoped></style>
