<template>
    <div class="merchant-apply-lists">
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item label="申请人" prop="name">
                    <el-input class="w-[280px]" v-model="queryParams.name" clearable placeholder="请输入商户名称" />
                </el-form-item>
                <el-form-item label="手机号" prop="mobile">
                    <el-input class="w-[280px]" v-model="queryParams.mobile" clearable placeholder="请输入联系电话" />
                </el-form-item>
                <el-form-item label="状态" prop="status">
                    <el-select class="w-[280px]" v-model="queryParams.status">
                        <el-option label="全部" value="" />
                        <el-option label="待审核" :value="0" />
                        <el-option label="已通过" :value="1" />
                        <el-option label="已拒绝" :value="2" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="ID" prop="id" min-width="80" />
                <el-table-column label="商户名称" prop="name" min-width="120" />
                <el-table-column label="联系电话" prop="mobile" min-width="120" />
                <el-table-column label="微信号" prop="wechat" min-width="120" />
                <el-table-column label="简介" prop="desc" min-width="200" show-overflow-tooltip />
                <el-table-column label="申请时间" prop="create_time" min-width="160" />
                <el-table-column label="状态" min-width="100">
                    <template #default="{ row }">
                        <el-tag v-if="row.status == 0" type="warning">待审核</el-tag>
                        <el-tag v-else-if="row.status == 1" type="success">已通过</el-tag>
                        <el-tag v-else type="danger">已拒绝</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="审核备注" prop="audit_remark" min-width="150" show-overflow-tooltip />
                <el-table-column label="审核时间" prop="update_time" min-width="160" />
                <el-table-column label="操作" width="180" fixed="right">
                    <template #default="{ row }">
                        <el-button v-if="row.status == 0" type="primary" link @click="openAudit(row)">审核</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>

        <el-dialog v-model="showAuditDialog" title="审核处理" width="500px">
            <el-form ref="auditFormRef" :model="auditForm" :rules="auditRules" label-width="100px">
                <el-form-item label="审核状态" prop="status">
                    <el-radio-group v-model="auditForm.status">
                        <el-radio :label="1">通过</el-radio>
                        <el-radio :label="2">拒绝</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="备注说明" prop="audit_remark" v-if="auditForm.status == 2">
                    <el-input 
                        v-model="auditForm.audit_remark" 
                        type="textarea" 
                        :rows="3" 
                        placeholder="请输入拒绝原因" 
                    />
                </el-form-item>
                <el-form-item label="备注说明" prop="audit_remark" v-else>
                    <el-input 
                        v-model="auditForm.audit_remark" 
                        type="textarea" 
                        :rows="3" 
                        placeholder="请输入备注（选填）" 
                    />
                </el-form-item>
            </el-form>
            <template #footer>
                <div class="dialog-footer">
                    <el-button @click="showAuditDialog = false">取消</el-button>
                    <el-button type="primary" @click="handleAuditSubmit">确定</el-button>
                </div>
            </template>
        </el-dialog>
    </div>
</template>

<script setup lang="ts">
import { applyLists, auditApply } from '@/api/merchant'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'
import type { FormInstance, FormRules } from 'element-plus'

const queryParams = reactive({
    name: '',
    mobile: '',
    status: ''
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: applyLists,
    params: queryParams
})

const showAuditDialog = ref(false)
const auditFormRef = ref<FormInstance>()
const auditForm = reactive({
    id: '',
    status: 1,
    audit_remark: ''
})

const auditRules = reactive<FormRules>({
    status: [{ required: true, message: '请选择审核状态', trigger: 'change' }],
    audit_remark: [{ 
        validator: (rule: any, value: any, callback: any) => {
            if (auditForm.status === 2 && !value) {
                callback(new Error('请输入拒绝原因'))
            } else {
                callback()
            }
        }, 
        trigger: 'blur' 
    }]
})

const openAudit = (row: any) => {
    auditForm.id = row.id
    auditForm.status = 1
    auditForm.audit_remark = ''
    showAuditDialog.value = true
}

const handleAuditSubmit = async () => {
    if (!auditFormRef.value) return
    await auditFormRef.value.validate()
    
    try {
        await auditApply(auditForm)
        feedback.msgSuccess('操作成功')
        showAuditDialog.value = false
        getLists()
    } catch (error) {
        // 错误已由拦截器处理
    }
}

getLists()
</script>
