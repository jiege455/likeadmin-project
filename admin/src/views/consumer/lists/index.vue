<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-form ref="formRef" class="mb-[-16px]" :model="queryParams" :inline="true">
                <el-form-item class="w-[280px]" label="用户信息">
                    <el-input
                        v-model="queryParams.keyword"
                        placeholder="用户编号/账号/昵称/手机号码"
                        clearable
                        @keyup.enter="resetPage"
                    />
                </el-form-item>
                <el-form-item label="注册时间">
                    <daterange-picker
                        v-model:startTime="queryParams.create_time_start"
                        v-model:endTime="queryParams.create_time_end"
                    />
                </el-form-item>
                <el-form-item class="w-[280px]" label="注册来源">
                    <el-select v-model="queryParams.channel">
                        <el-option
                            v-for="(item, key) in ClientMap"
                            :key="key"
                            :label="item"
                            :value="key"
                        />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="resetPage">查询</el-button>
                    <el-button @click="resetParams">重置</el-button>
                    <el-button v-perms="['user.user/add']" type="success" @click="handleAdd">新增用户</el-button>
                    <export-data
                        class="ml-2.5"
                        :fetch-fun="getUserList"
                        :params="queryParams"
                        :page-size="pager.size"
                    />
                </el-form-item>
            </el-form>
        </el-card>
        <el-card class="!border-none mt-4" shadow="never">
            <el-table size="large" v-loading="pager.loading" :data="pager.lists">
                <el-table-column label="ID" prop="id" min-width="60" />
                <el-table-column label="用户编号" prop="sn" min-width="100" />
                <el-table-column label="头像" min-width="100">
                    <template #default="{ row }">
                        <el-avatar :src="row.avatar" :size="50" />
                    </template>
                </el-table-column>
                <el-table-column label="昵称" prop="nickname" min-width="100" />
                <el-table-column label="账号" prop="account" min-width="120" />
                <el-table-column label="是否商家" min-width="100">
                    <template #default="{ row }">
                        <el-tag v-if="row.is_merchant == 1" type="success">是</el-tag>
                        <el-tag v-else type="info">否</el-tag>
                    </template>
                </el-table-column>
                <el-table-column label="手机号码" prop="mobile" min-width="100" />
                <el-table-column label="上级邀请人" prop="inviter_nickname" min-width="150" />
                <el-table-column label="注册来源" prop="channel" min-width="100" />
                <el-table-column label="注册时间" prop="create_time" min-width="120" />
                <el-table-column label="操作" width="120" fixed="right">
                    <template #default="{ row }">
                        <el-button v-perms="['user.user/detail']" type="primary" link>
                            <router-link
                                :to="{
                                    path: getRoutePath('user.user/detail'),
                                    query: {
                                        id: row.id
                                    }
                                }"
                            >
                                详情
                            </router-link>
                        </el-button>
                        <el-button v-perms="['user.user/delete']" type="danger" link @click="handleDelete(row.id)">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end mt-4">
                <pagination v-model="pager" @change="getLists" />
            </div>
        </el-card>

        <popup
            ref="popupRef"
            title="新增用户"
            :async="true"
            width="500px"
            @confirm="handleSubmit"
        >
            <el-form ref="formRef" :model="formData" :rules="formRules" label-width="80px">
                <el-form-item label="账号" prop="account">
                    <el-input v-model="formData.account" placeholder="请输入账号(3-20位字母数字)" />
                </el-form-item>
                <el-form-item label="密码" prop="password">
                    <el-input v-model="formData.password" type="password" placeholder="请输入密码(6-20位)" show-password />
                </el-form-item>
                <el-form-item label="确认密码" prop="password_confirm">
                    <el-input v-model="formData.password_confirm" type="password" placeholder="请再次输入密码" show-password />
                </el-form-item>
                <el-form-item label="昵称" prop="nickname">
                    <el-input v-model="formData.nickname" placeholder="请输入昵称" />
                </el-form-item>
                <el-form-item label="手机号码" prop="mobile">
                    <el-input v-model="formData.mobile" placeholder="请输入手机号码" />
                </el-form-item>
                <el-form-item label="真实姓名" prop="real_name">
                    <el-input v-model="formData.real_name" placeholder="请输入真实姓名" />
                </el-form-item>
            </el-form>
        </popup>
    </div>
</template>
<script lang="ts" setup name="consumerLists">
import { getUserList, userDelete, userAdd } from '@/api/consumer'
import { ClientMap } from '@/enums/appEnums'
import { usePaging } from '@/hooks/usePaging'
import { getRoutePath } from '@/router'
import feedback from '@/utils/feedback'
import Popup from '@/components/popup/index.vue'
import type { FormInstance, FormRules } from 'element-plus'

const queryParams = reactive({
    keyword: '',
    channel: '',
    create_time_start: '',
    create_time_end: ''
})

const { pager, getLists, resetPage, resetParams } = usePaging({
    fetchFun: getUserList,
    params: queryParams
})

const popupRef = ref<InstanceType<typeof Popup>>()
const formRef = ref<FormInstance>()
const formData = reactive({
    account: '',
    password: '',
    password_confirm: '',
    nickname: '',
    mobile: '',
    real_name: ''
})

const formRules: FormRules = {
    account: [{ required: true, message: '请输入账号', trigger: 'blur' }],
    password: [{ required: true, message: '请输入密码', trigger: 'blur' }],
    password_confirm: [
        { required: true, message: '请确认密码', trigger: 'blur' },
        {
            validator: (rule: any, value: any, callback: any) => {
                if (value !== formData.password) {
                    callback(new Error('两次输入密码不一致'))
                } else {
                    callback()
                }
            },
            trigger: 'blur'
        }
    ]
}

const handleAdd = () => {
    formData.account = ''
    formData.password = ''
    formData.password_confirm = ''
    formData.nickname = ''
    formData.mobile = ''
    formData.real_name = ''
    popupRef.value?.open()
}

const handleSubmit = async () => {
    await formRef.value?.validate()
    await userAdd(formData)
    feedback.msgSuccess('新增成功')
    popupRef.value?.close()
    getLists()
}

const handleDelete = async (id: number) => {
    await feedback.confirm('确定要删除该用户吗？')
    await userDelete({ id })
    feedback.msgSuccess('删除成功')
    getLists()
}

onActivated(() => {
    getLists()
})

getLists()
</script>
