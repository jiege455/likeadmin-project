<!--
  PC端装修设置
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
-->
<template>
    <div class="decoration-pc min-w-[1100px]">
        <el-card shadow="never" class="!border-none flex-1 flex">
            <div class="text-xl font-medium">首页装修</div>
            <router-link
                :to="{
                    path: '/decoration/pc_details',
                    query: {
                        url: state.pc_url
                    }
                }"
            >
                <el-button class="m-5" type="primary" size="large">去装修</el-button>
            </router-link>
            <el-form label-width="100px">
                <el-form-item label="最近更新">{{ state.update_time }}</el-form-item>
                <el-form-item label="PC端链接">
                    <el-input style="width: 350px" v-model="state.pc_url" disabled></el-input>
                    <el-button type="primary" v-copy="state.pc_url">复制</el-button>
                </el-form-item>
                <el-divider />
                <el-form-item label="PC端访问">
                    <el-switch
                        v-model="state.pc_open"
                        :active-value="1"
                        :inactive-value="0"
                        active-text="开启"
                        inactive-text="关闭"
                        @change="handlePcOpenChange"
                    />
                </el-form-item>
                <el-form-item v-if="!state.pc_open">
                    <el-alert
                        title="关闭后，用户访问PC端将跳转到移动端页面"
                        type="warning"
                        :closable="false"
                        show-icon
                    />
                </el-form-item>
                <el-form-item label="关闭提示语" v-if="!state.pc_open">
                    <el-input
                        style="width: 350px"
                        v-model="state.pc_close_tips"
                        placeholder="请输入关闭提示语"
                        maxlength="100"
                        show-word-limit
                        @blur="handleSaveTips"
                    />
                </el-form-item>
            </el-form>
        </el-card>
    </div>
</template>
<script lang="ts" setup name="decorationPc">
import { getDecoratePc, setDecoratePcOpen } from '@/api/decoration'
import feedback from '@/utils/feedback'

const state = ref({
    update_time: '',
    pc_url: '',
    pc_open: 1,
    pc_close_tips: 'PC端暂未开放，请使用手机访问'
})

const getData = async () => {
    try {
        const data = await getDecoratePc()
        state.value = {
            update_time: data.update_time || '',
            pc_url: data.pc_url || '',
            pc_open: data.pc_open ?? 1,
            pc_close_tips: data.pc_close_tips || 'PC端暂未开放，请使用手机访问'
        }
    } catch (error) {
        console.error('获取数据失败:', error)
    }
}

const handlePcOpenChange = async (val: number) => {
    try {
        await setDecoratePcOpen({
            pc_open: val,
            pc_close_tips: state.value.pc_close_tips
        })
        feedback.msgSuccess(val === 1 ? '已开启PC端访问' : '已关闭PC端访问')
    } catch (error) {
        state.value.pc_open = val === 1 ? 0 : 1
    }
}

const handleSaveTips = async () => {
    if (!state.value.pc_close_tips.trim()) {
        state.value.pc_close_tips = 'PC端暂未开放，请使用手机访问'
    }
    try {
        await setDecoratePcOpen({
            pc_open: state.value.pc_open,
            pc_close_tips: state.value.pc_close_tips
        })
    } catch (error) {
        console.error('保存失败:', error)
    }
}

getData()
</script>

<style lang="scss" scoped>
.decoration-pc {
    :deep(.el-divider) {
        margin: 16px 0;
    }
}
</style>
