<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-alert
                type="warning"
                title="温馨提示：商家提现申请管理"
                :closable="false"
                show-icon
            ></el-alert>
            <el-form ref="formRef" class="mb-[-16px] mt-[16px]" :model="queryParams" :inline="true">
                <el-form-item class="w-[280px]" label="商家信息">
                    <el-input
                        v-model="queryParams.keyword"
                        placeholder="商家名称/账号"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item class="w-[280px]" label="状态">
                    <el-select v-model="queryParams.status">
                        <el-option label="全部" value />
                        <el-option label="待审核" :value="0" />
                        <el-option label="已拒绝" :value="1" />
                        <el-option label="已通过" :value="2" />
                        <el-option label="已打款" :value="3" />
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
                <el-table-column label="商家名称" prop="merchant_name" min-width="120" />
                <el-table-column label="提现金额" prop="money" min-width="100" />
                <el-table-column label="剩余余额" prop="left_money" min-width="100" />
                <el-table-column label="提现方式" prop="type_desc" min-width="100" />
                <el-table-column label="账号信息" prop="account_info" min-width="200" show-overflow-tooltip />
                <el-table-column label="状态" prop="status_text" min-width="100">
                     <template #default="{ row }">
                        <el-tag v-if="row.status == 0" type="warning">{{ row.status_text }}</el-tag>
                        <el-tag v-else-if="row.status == 1" type="danger">{{ row.status_text }}</el-tag>
                        <el-tag v-else-if="row.status == 2" type="success">{{ row.status_text }}</el-tag>
                        <el-tag v-else-if="row.status == 3" type="info">{{ row.status_text }}</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="申请时间" prop="create_time" min-width="160" />
                <el-table-column label="操作" width="120" fixed="right">
                    <template #default="{ row }">
                        <el-button
                            v-if="row.status == 0"
                            v-perms="['finance.merchant_withdraw/audit']"
                            type="primary"
                            link
                            @click="handleAudit(row)"
                        >
                            审核
                        </el-button>
                        <el-button
                            v-if="row.status == 2"
                            v-perms="['finance.merchant_withdraw/audit']"
                            type="success"
                            link
                            @click="handlePay(row)"
                        >
                            确认打款
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>

        <el-dialog v-model="showAudit" title="提现审核" width="500px">
            <el-form :model="auditForm" label-width="80px">
                <el-form-item label="审核状态">
                    <el-radio-group v-model="auditForm.status">
                        <el-radio :label="2">通过</el-radio>
                        <el-radio :label="1">拒绝</el-radio>
                    </el-radio-group>
                </el-form-item>
                <el-form-item label="备注">
                    <el-input type="textarea" v-model="auditForm.remark" placeholder="请输入备注或拒绝原因" />
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="showAudit = false">取消</el-button>
                    <el-button type="primary" @click="submitAudit">确定</el-button>
                </span>
            </template>
        </el-dialog>

        <el-dialog v-model="showPay" title="确认打款" width="500px">
            <el-form label-width="80px">
                <el-form-item label="提现金额">
                    <span class="text-lg font-bold text-red-500">¥{{ payForm.money }}</span>
                </el-form-item>
                <el-form-item label="提现方式">
                    <span>{{ payForm.type_text }}</span>
                </el-form-item>
                <el-form-item label="收款账号">
                    <span>{{ payForm.account_info }}</span>
                </el-form-item>
                <el-form-item label="打款备注">
                    <el-input type="textarea" v-model="payForm.remark" placeholder="请输入打款备注（选填）" />
                </el-form-item>
            </el-form>
            <template #footer>
                <span class="dialog-footer">
                    <el-button @click="showPay = false">取消</el-button>
                    <el-button type="success" @click="submitPay">确认已打款</el-button>
                </span>
            </template>
        </el-dialog>
    </div>
</template>
<script lang="ts" setup name="merchantWithdraw">
import { merchantWithdrawLists, merchantWithdrawAudit } from '@/api/finance/merchantWithdraw'
import { usePaging } from '@/hooks/usePaging'
import feedback from '@/utils/feedback'

const queryParams = reactive({
    keyword: '',
    status: '',
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: merchantWithdrawLists,
    params: queryParams
})

const showAudit = ref(false)
const auditForm = reactive({
    id: 0,
    status: 2,
    remark: ''
})

const showPay = ref(false)
const payForm = reactive({
    id: 0,
    money: 0,
    type_text: '',
    account_info: '',
    remark: ''
})

const handleAudit = (row: any) => {
    auditForm.id = row.id
    auditForm.status = 2
    auditForm.remark = ''
    showAudit.value = true
}

const handlePay = (row: any) => {
    payForm.id = row.id
    payForm.money = row.money
    payForm.type_text = row.type_text
    
    // 根据提现方式显示不同的账号信息
    if (row.type === 1) {
        // 微信零钱
        payForm.account_info = row.account_info || '微信零钱'
    } else if (row.type === 2) {
        // 支付宝
        payForm.account_info = row.account_info || '支付宝账号'
    } else if (row.type === 3) {
        // 银行卡
        const bankInfo = []
        if (row.bank_name) bankInfo.push(row.bank_name)
        if (row.bank_branch) bankInfo.push(row.bank_branch)
        if (row.bank_account) bankInfo.push(row.bank_account)
        if (row.bank_user) bankInfo.push(row.bank_user)
        payForm.account_info = bankInfo.join(' ') || row.account_info
    } else {
        payForm.account_info = row.account_info
    }
    
    payForm.remark = ''
    showPay.value = true
}

const submitAudit = async () => {
    try {
        await merchantWithdrawAudit(auditForm)
        feedback.msgSuccess('操作成功')
        showAudit.value = false
        getLists()
    } catch (error: any) {
        feedback.msgError(error?.msg || '操作失败')
    }
}

const submitPay = async () => {
    try {
        await merchantWithdrawAudit({
            id: payForm.id,
            status: 3,
            remark: payForm.remark
        })
        feedback.msgSuccess('打款成功')
        showPay.value = false
        getLists()
    } catch (error: any) {
        feedback.msgError(error?.msg || '打款确认失败')
    }
}

getLists()
</script>
