<!--
    文章提示设置
    开发者：杰哥网络科技
    QQ：2711793818 杰哥
-->
<template>
    <div class="article-tips-setting">
        <el-card class="!border-none mb-4" shadow="never">
            <template #header>
                <div class="flex items-center justify-between">
                    <span class="font-medium">顶部注意事项</span>
                    <el-switch v-model="formData.top_tips_show" :active-value="1" :inactive-value="0" active-text="显示" inactive-text="隐藏" />
                </div>
            </template>
            <div class="form-tips mb-4">显示在文章标题下方，用于提醒用户注意事项</div>
            <editor v-model="formData.top_tips" height="300" placeholder="请输入顶部注意事项内容"></editor>
        </el-card>

        <el-card class="!border-none mb-4" shadow="never">
            <template #header>
                <div class="flex items-center justify-between">
                    <span class="font-medium">底部购买须知</span>
                    <el-switch v-model="formData.bottom_tips_show" :active-value="1" :inactive-value="0" active-text="显示" inactive-text="隐藏" />
                </div>
            </template>
            <div class="form-tips mb-4">显示在文章内容下方，用于展示购买须知、购买指南等信息</div>
            <editor v-model="formData.bottom_tips" height="300" placeholder="请输入底部购买须知内容"></editor>
        </el-card>

        <footer-btns v-perms="['setting.article_tips/setConfig']">
            <el-button type="primary" @click="handleSave">保存设置</el-button>
        </footer-btns>
    </div>
</template>

<script setup lang="ts" name="articleTips">
import { getArticleTips, setArticleTips } from '@/api/setting/article_tips'

const formData = reactive({
    top_tips: '',
    top_tips_show: 1,
    bottom_tips: '',
    bottom_tips_show: 1
})

const getData = async () => {
    const data = await getArticleTips()
    Object.assign(formData, data)
}

const handleSave = async () => {
    await setArticleTips({ ...formData })
    getData()
}

getData()
</script>

<style lang="scss" scoped>
.article-tips-setting {
    :deep(.el-card__header) {
        padding: 16px 20px;
    }
}
</style>
