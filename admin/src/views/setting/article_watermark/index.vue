<!--
    文章水印设置
    开发者：杰哥网络科技
    QQ：2711793818 杰哥
-->
<template>
    <div class="article-watermark-setting">
        <el-card class="!border-none mb-4" shadow="never">
            <template #header>
                <div class="flex items-center justify-between">
                    <span class="font-medium">水印设置</span>
                    <el-switch v-model="formData.enable" :active-value="1" :inactive-value="0" active-text="开启" inactive-text="关闭" />
                </div>
            </template>
            <div class="form-tips mb-4">开启后，文章详情页将显示水印，防止用户截图盗版</div>

            <el-form :model="formData" label-width="120px">
                <el-form-item label="水印文字">
                    <el-input v-model="formData.text" placeholder="请输入水印文字" class="max-w-80" />
                </el-form-item>

                <el-form-item label="联系方式">
                    <el-input v-model="formData.contact" placeholder="请输入联系方式" class="max-w-80" />
                </el-form-item>

                <el-form-item label="水印位置">
                    <div class="position-hint">
                        <el-icon><Warning /></el-icon>
                        <span>前端固定为斜向平铺显示</span>
                    </div>
                </el-form-item>

                <el-form-item label="透明度">
                    <el-slider v-model="formData.opacity" :min="0.05" :max="0.3" :step="0.01" class="max-w-80" />
                    <span class="ml-4 text-gray-400">当前: {{ (formData.opacity * 100).toFixed(0) }}%</span>
                </el-form-item>

                <el-form-item label="水印预览">
                    <div class="preview-box" :style="previewStyle">
                        <span class="preview-text">{{ formData.text }}</span>
                        <span class="preview-contact">{{ formData.contact }}</span>
                    </div>
                </el-form-item>
            </el-form>
        </el-card>

        <footer-btns v-perms="['setting.article_watermark/setConfig']">
            <el-button type="primary" @click="handleSave">保存设置</el-button>
        </footer-btns>
    </div>
</template>

<script setup lang="ts" name="articleWatermark">
import { getArticleWatermark, setArticleWatermark } from '@/api/setting/article_watermark'
import { Warning } from '@element-plus/icons-vue'

const formData = reactive({
    enable: 0,
    text: '杰哥网络科技',
    contact: 'QQ:2711793818',
    position: 'right_bottom',
    opacity: 0.15
})

const previewStyle = computed(() => {
    const baseStyle: any = {
        opacity: formData.opacity
    }
    if (formData.position === 'right_bottom') {
        baseStyle.justifyContent = 'flex-end'
        baseStyle.alignItems = 'flex-end'
    } else if (formData.position === 'left_top') {
        baseStyle.justifyContent = 'flex-start'
        baseStyle.alignItems = 'flex-start'
    } else {
        baseStyle.justifyContent = 'center'
        baseStyle.alignItems = 'center'
    }
    return baseStyle
})

const getData = async () => {
    const data = await getArticleWatermark()
    Object.assign(formData, data)
}

const handleSave = async () => {
    await setArticleWatermark({ ...formData })
    getData()
}

getData()
</script>

<style lang="scss" scoped>
.article-watermark-setting {
    :deep(.el-card__header) {
        padding: 16px 20px;
    }
}

.preview-box {
    width: 300px;
    height: 150px;
    background: #f5f5f5;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    color: #999;
    font-size: 12px;
}

.preview-text {
    font-size: 14px;
    font-weight: 500;
}

.preview-contact {
    margin-top: 4px;
}

.position-hint {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #909399;
    font-size: 13px;

    .el-icon {
        font-size: 16px;
    }
}
</style>