<template>
    <div>
        <el-card class="!border-none" shadow="never">
            <el-page-header content="用户详情" @back="$router.back()" />
        </el-card>
        <el-card class="mt-4 !border-none" header="基本资料" shadow="never">
            <el-form ref="formRef" class="ls-form" :model="formData" label-width="120px">
                <div class="bg-page flex py-5 mb-10 items-center">
                    <div class="basis-40 flex flex-col justify-center items-center">
                        <div class="mb-2 text-tx-regular">用户头像</div>
                        <el-avatar :src="formData.avatar" :size="58" />
                    </div>
                    <div class="basis-40 flex flex-col justify-center items-center">
                        <div class="text-tx-regular">账户余额</div>
                        <div class="mt-2 flex items-center">
                            ¥{{ formData.user_money }}
                            <el-button
                                v-perms="['user.user/adjustMoney']"
                                type="primary"
                                link
                                @click="handleAdjust(formData.user_money)"
                            >
                                调整
                            </el-button>
                        </div>
                    </div>
                    <div class="basis-40 flex flex-col justify-center items-center">
                        <div class="text-tx-regular">商家身份</div>
                        <div class="mt-2">
                            <el-tag v-if="formData.is_merchant === 1" type="success">是商家</el-tag>
                            <el-tag v-else type="info">普通用户</el-tag>
                        </div>
                    </div>
                </div>
                <el-form-item label="用户编号：">
                    {{ formData.sn }}
                    <div class="ml-2 text-xs text-tx-secondary copy-btn" v-copy="formData.sn">
                        复制
                    </div>
                </el-form-item>
                <el-form-item label="用户昵称：">
                    {{ formData.nickname }}
                </el-form-item>
                <el-form-item label="账号：">
                    {{ formData.account }}
                    <popover-input
                        class="ml-[10px]"
                        @confirm="handleEdit($event, 'account')"
                        :limit="32"
                        v-perms="['user.user/edit']"
                    >
                        <el-button type="primary" link>
                            <icon name="el-icon-EditPen" />
                        </el-button>
                    </popover-input>
                </el-form-item>
                <el-form-item label="真实姓名：">
                    {{ formData.real_name || '-' }}
                    <popover-input
                        class="ml-[10px]"
                        @confirm="handleEdit($event, 'real_name')"
                        :limit="32"
                        v-perms="['user.user/edit']"
                    >
                        <el-button type="primary" link>
                            <icon name="el-icon-EditPen" />
                        </el-button>
                    </popover-input>
                </el-form-item>
                <el-form-item label="性别：">
                    {{ formData.sex }}
                    <popover-input
                        class="ml-[10px]"
                        type="select"
                        :options="[
                            {
                                label: '未知',
                                value: 0
                            },
                            {
                                label: '男',
                                value: 1
                            },
                            {
                                label: '女',
                                value: 2
                            }
                        ]"
                        @confirm="handleEdit($event, 'sex')"
                        v-perms="['user.user/edit']"
                    >
                        <el-button type="primary" link>
                            <icon name="el-icon-EditPen" />
                        </el-button>
                    </popover-input>
                </el-form-item>
                <el-form-item label="联系电话：">
                    {{ formData.mobile || '-' }}
                    <popover-input
                        class="ml-[10px]"
                        type="number"
                        @confirm="handleEdit($event, 'mobile')"
                        v-perms="['user.user/edit']"
                    >
                        <el-button type="primary" link>
                            <icon name="el-icon-EditPen" />
                        </el-button>
                    </popover-input>
                </el-form-item>
                <el-form-item label="注册来源："> {{ formData.channel }} </el-form-item>
                <el-form-item label="注册时间："> {{ formData.create_time }} </el-form-item>
                <el-form-item label="最近登录时间："> {{ formData.login_time }} </el-form-item>
            </el-form>
        </el-card>

        <el-card class="mt-4 !border-none" header="商家信息" shadow="never" v-if="formData.is_merchant === 1">
            <el-form class="ls-form" label-width="120px">
                <el-form-item label="商家ID：">
                    {{ formData.merchant_id }}
                </el-form-item>
                <el-form-item label="商家名称：">
                    {{ formData.merchant_name || '-' }}
                </el-form-item>
            </el-form>
        </el-card>

        <el-card class="mt-4 !border-none" header="邀请人信息" shadow="never">
            <el-form class="ls-form" label-width="120px">
                <template v-if="formData.inviter_info">
                    <el-form-item label="邀请人ID：">
                        {{ formData.inviter_info.id }}
                    </el-form-item>
                    <el-form-item label="邀请人编号：">
                        {{ formData.inviter_info.sn }}
                    </el-form-item>
                    <el-form-item label="邀请人昵称：">
                        <div class="flex items-center">
                            <el-avatar :src="formData.inviter_info.avatar" :size="24" class="mr-2" />
                            {{ formData.inviter_info.nickname }}
                        </div>
                    </el-form-item>
                    <el-form-item label="邀请人手机：">
                        {{ formData.inviter_info.mobile || '-' }}
                    </el-form-item>
                </template>
                <template v-else>
                    <el-form-item label="邀请人：">
                        <span class="text-tx-secondary">无上级邀请人</span>
                    </el-form-item>
                </template>
            </el-form>
        </el-card>

        <account-adjust
            v-model:show="adjustState.show"
            :value="adjustState.value"
            @confirm="handleConfirmAdjust"
        />
    </div>
</template>

<script lang="ts" setup name="consumerDetail">
import type { FormInstance } from 'element-plus'

import { adjustMoney, getUserDetail, userEdit } from '@/api/consumer'
import { isEmpty } from '@/utils/util'

import AccountAdjust from '../components/account-adjust.vue'

const route = useRoute()
const formData = reactive({
    avatar: '',
    channel: '',
    create_time: '',
    login_time: '',
    mobile: '',
    nickname: '',
    real_name: '',
    sex: 0,
    sn: '',
    account: '',
    user_money: '',
    is_merchant: 0,
    merchant_id: 0,
    merchant_name: '',
    inviter_id: 0,
    inviter_info: null as any
})

const adjustState = reactive({
    show: false,
    value: ''
})
const formRef = shallowRef<FormInstance>()

const getDetails = async () => {
    const data = await getUserDetail({
        id: route.query.id
    })
    Object.keys(formData).forEach((key) => {
        //@ts-ignore
        formData[key] = data[key]
    })
}

const handleEdit = async (value: string, field: string) => {
    if (isEmpty(value)) return
    await userEdit({
        id: route.query.id,
        field,
        value
    })
    getDetails()
}

const handleAdjust = (value: string) => {
    adjustState.show = true
    adjustState.value = value
}
const handleConfirmAdjust = async (value: any) => {
    await adjustMoney({ user_id: route.query.id, ...value })
    adjustState.show = false
    getDetails()
}
getDetails()
</script>
