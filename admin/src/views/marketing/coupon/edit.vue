<template>
    <el-dialog v-model="visible" :title="mode == 'add' ? '新增优惠券' : '编辑优惠券'" width="600px">
        <el-form ref="formRef" :model="formData" :rules="rules" label-width="100px">
            <el-form-item label="名称" prop="name">
                <el-input v-model="formData.name" placeholder="请输入优惠券名称" />
            </el-form-item>
            <el-form-item label="面额" prop="money">
                <el-input-number v-model="formData.money" :min="0.01" :precision="2" />
                <span class="ml-2">元</span>
            </el-form-item>
            <el-form-item label="使用门槛" prop="condition_money">
                <el-input-number v-model="formData.condition_money" :min="0" :precision="2" />
                <span class="ml-2">元 (0为无门槛)</span>
            </el-form-item>
            <el-form-item label="发放总量" prop="total_count">
                <el-input-number v-model="formData.total_count" :min="1" :precision="0" />
                <span class="ml-2">张</span>
            </el-form-item>
            <el-form-item label="有效期类型" prop="use_time_type">
                <el-radio-group v-model="formData.use_time_type">
                    <el-radio :label="1">固定时间</el-radio>
                    <el-radio :label="2">领券后天数</el-radio>
                </el-radio-group>
            </el-form-item>
            <el-form-item v-if="formData.use_time_type == 1" label="固定时间" prop="use_time_start">
                <el-date-picker
                    v-model="timeRange"
                    type="datetimerange"
                    range-separator="至"
                    start-placeholder="开始时间"
                    end-placeholder="结束时间"
                    value-format="YYYY-MM-DD HH:mm:ss"
                />
            </el-form-item>
            <el-form-item v-if="formData.use_time_type == 2" label="有效天数" prop="use_days">
                <el-input-number v-model="formData.use_days" :min="1" :precision="0" />
                <span class="ml-2">天</span>
            </el-form-item>
            <el-form-item label="状态" prop="status">
                <el-switch v-model="formData.status" :active-value="1" :inactive-value="0" />
            </el-form-item>
        </el-form>
        <template #footer>
            <el-button @click="visible = false">取消</el-button>
            <el-button type="primary" @click="handleSubmit">确定</el-button>
        </template>
    </el-dialog>
</template>

<script lang="ts" setup>
import { ref, reactive } from 'vue'
import { couponAdd, couponEdit } from '@/api/marketing/coupon'
import feedback from '@/utils/feedback'

const emit = defineEmits(['success', 'close'])
const visible = ref(false)
const mode = ref('add')
const formRef = ref()
const timeRange = ref<any>([])

const formData = reactive({
    id: '',
    name: '',
    money: 10,
    condition_money: 100,
    total_count: 100,
    use_time_type: 1, // 1-固定, 2-天数
    use_days: 7,
    status: 1,
    use_time_start: '',
    use_time_end: ''
})

const rules = {
    name: [{ required: true, message: '请输入名称', trigger: 'blur' }],
    money: [{ required: true, message: '请输入面额', trigger: 'blur' }],
    total_count: [{ required: true, message: '请输入总量', trigger: 'blur' }]
}

const open = (type: string, data?: any) => {
    mode.value = type
    visible.value = true
    if (type == 'edit') {
        Object.assign(formData, data)
        if (data.use_time_type == 1 && data.use_time_start) {
             // 处理时间回显逻辑，需要后端返回具体时间字符串
             // 这里假设后端返回的是时间戳或者格式化时间
             // 实际上需要转换
        }
    } else {
        // 重置表单
        formData.id = ''
        formData.name = ''
        formData.money = 10
        formData.condition_money = 100
        formData.total_count = 100
        formData.use_time_type = 1
        formData.use_days = 7
        formData.status = 1
        timeRange.value = []
    }
}

const handleSubmit = async () => {
    await formRef.value?.validate()
    
    if (formData.use_time_type == 1) {
        if (!timeRange.value || timeRange.value.length < 2) {
            return feedback.msgError('请选择有效时间')
        }
        formData.use_time_start = timeRange.value[0]
        formData.use_time_end = timeRange.value[1]
    }

    if (mode.value == 'add') {
        await couponAdd(formData)
    } else {
        await couponEdit(formData)
    }
    
    feedback.msgSuccess('操作成功')
    visible.value = false
    emit('success')
}

defineExpose({ open })
</script>
