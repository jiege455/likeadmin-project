<!-- 
  登录注册配置页面
  开发者：杰哥网络科技
  QQ: 2711793818
-->
<template>
    <div class="login-register">
        <el-form ref="formRef" :rules="rules" :model="formData" label-width="120px">
            <el-card shadow="never" class="!border-none">
                <div class="font-medium mb-7">通用设置</div>

                <el-form-item label="登录方式" prop="login_way">
                    <div>
                        <el-checkbox-group v-model="formData.login_way" @change="handleLoginWayChange">
                            <el-checkbox value="1">账号密码登录</el-checkbox>
                            <el-checkbox value="2">手机验证码登录</el-checkbox>
                        </el-checkbox-group>
                        <div class="form-tips">系统通用登录方式，至少选择一项</div>
                    </div>
                </el-form-item>

                <el-form-item label="强制绑定手机" prop="coerce_mobile">
                    <div>
                        <el-switch
                            v-model="switchState.coerce_mobile"
                            @change="(val: any) => handleSwitchChange('coerce_mobile', val)"
                            active-text="开启"
                            inactive-text="关闭"
                        />
                        <div class="form-tips">
                            1、如果开启，则新用户在注册完成之后要强制绑定手机号<br />
                            2、老用户登录时检测到没有绑定手机，则要重新绑定手机号
                        </div>
                    </div>
                </el-form-item>

                <el-form-item label="政策协议" prop="login_agreement">
                    <div>
                        <el-switch
                            v-model="switchState.login_agreement"
                            @change="(val: any) => handleSwitchChange('login_agreement', val)"
                            active-text="开启"
                            inactive-text="关闭"
                        />
                        <div class="form-tips">登录/注册会员时，是否显示服务协议和隐私政策</div>
                    </div>
                </el-form-item>
            </el-card>

            <!-- 注册验证设置 -->
            <el-card shadow="never" class="!border-none mt-4">
                <template #header>
                    <div class="card-header">
                        <el-icon><User /></el-icon>
                        <span>注册验证设置</span>
                    </div>
                </template>
                <el-form label-width="150px">
                    <el-form-item label="用户注册开关">
                        <el-switch
                            v-model="switchState.register_open"
                            @change="(val: any) => handleSwitchChange('register_open', val)"
                            active-text="开启"
                            inactive-text="关闭"
                        />
                        <div class="form-tips">关闭后用户无法注册新账号</div>
                    </el-form-item>

                    <el-form-item label="注册验证方式">
                        <el-radio-group v-model="formData.register_verify_type" @change="(val: any) => handleRadioChange('register_verify_type', val)">
                            <el-radio label="email">邮箱验证码验证（推荐）</el-radio>
                            <el-radio label="mobile">手机验证码验证</el-radio>
                        </el-radio-group>
                        <div class="form-tips">
                            <el-icon><InfoFilled /></el-icon>
                            选择"邮箱验证"后，注册时需要填写邮箱并接收验证码
                            <br/>
                            <el-link type="primary" @click="$router.push('/setting/email')">前往配置邮箱服务</el-link>
                        </div>
                    </el-form-item>

                    <el-form-item label="商家入驻开关">
                        <el-switch
                            v-model="switchState.merchant_apply_open"
                            @change="(val: any) => handleSwitchChange('merchant_apply_open', val)"
                            active-text="开启"
                            inactive-text="关闭"
                        />
                        <span class="mt-1 ml-2">
                            {{ switchState.merchant_apply_open ? '开启' : '关闭' }}
                        </span>
                        <div class="form-tips">关闭后用户无法申请商家入驻</div>
                    </el-form-item>

                    <el-form-item label="商家入驻验证方式">
                        <el-radio-group v-model="formData.merchant_apply_verify_type" @change="(val: any) => handleRadioChange('merchant_apply_verify_type', val)">
                            <el-radio label="email">邮箱验证码验证（推荐）</el-radio>
                            <el-radio label="mobile">手机验证码验证</el-radio>
                        </el-radio-group>
                    </el-form-item>

                    <el-form-item label="分销员申请验证方式">
                        <el-radio-group v-model="formData.distributor_apply_verify_type" @change="(val: any) => handleRadioChange('distributor_apply_verify_type', val)">
                            <el-radio label="email">邮箱验证码验证（推荐）</el-radio>
                            <el-radio label="mobile">手机验证码验证</el-radio>
                        </el-radio-group>
                    </el-form-item>

                    <el-form-item label="提现验证方式">
                        <el-radio-group v-model="formData.withdraw_verify_type" @change="(val: any) => handleRadioChange('withdraw_verify_type', val)">
                            <el-radio label="email">邮箱验证码验证（推荐）</el-radio>
                            <el-radio label="mobile">手机验证码验证</el-radio>
                        </el-radio-group>
                    </el-form-item>
                </el-form>
            </el-card>

            <!-- 通知设置 -->
            <el-card shadow="never" class="!border-none mt-4">
                <template #header>
                    <div class="card-header">
                        <el-icon><Message /></el-icon>
                        <span>通知设置</span>
                    </div>
                </template>
                <el-form label-width="150px">
                    <el-form-item label="邮件通知总开关">
                        <el-switch
                            v-model="switchState.email_notify_open"
                            @change="(val: any) => handleSwitchChange('email_notify_open', val)"
                            active-text="开启"
                            inactive-text="关闭"
                        />
                        <div class="form-tips">
                            <el-icon><InfoFilled /></el-icon>
                            关闭后所有邮件通知将停止发送（包括注册验证码、订单通知等）
                            <br/>
                            <el-link type="primary" @click="$router.push('/setting/email')">前往配置邮箱服务</el-link>
                        </div>
                    </el-form-item>

                    <el-form-item label="短信通知总开关">
                        <el-switch
                            v-model="switchState.sms_notify_open"
                            @change="(val: any) => handleSwitchChange('sms_notify_open', val)"
                            active-text="开启"
                            inactive-text="关闭"
                        />
                        <div class="form-tips">
                            <el-icon><InfoFilled /></el-icon>
                            关闭后所有短信通知将停止发送
                            <br/>
                            <el-link type="primary" @click="$router.push('/message/short_letter')">前往配置短信服务</el-link>
                        </div>
                    </el-form-item>
                </el-form>
            </el-card>

            <el-card shadow="never" class="!border-none mt-4">
                <div class="font-medium mb-7">第三方设置</div>

                <el-form-item label="第三方登录" prop="third_auth">
                    <div>
                        <el-switch
                            v-model="switchState.third_auth"
                            @change="(val: any) => handleSwitchChange('third_auth', val)"
                            active-text="开启"
                            inactive-text="关闭"
                        />
                        <div class="form-tips">登录时支持第三方登录，新用户授权即自动注册账号</div>

                        <div class="mt-2">
                            <el-checkbox
                                v-model="switchState.wechat_auth"
                                @change="(val: any) => handleSwitchChange('wechat_auth', val)"
                            >
                                微信登录
                            </el-checkbox>
                        </div>
                    </div>
                </el-form-item>

                <el-form-item label="聚合登录" prop="social_login">
                    <div>
                        <el-switch
                            v-model="switchState.social_login"
                            @change="(val: any) => handleSwitchChange('social_login', val)"
                            active-text="开启"
                            inactive-text="关闭"
                        />
                        <div class="form-tips">开启后支持QQ、微信、支付宝、百度、微软等第三方聚合登录</div>
                    </div>
                </el-form-item>

                <el-form-item label="聚合登录配置" v-if="switchState.social_login" prop="social_login_appid">
                    <div class="w-full">
                        <el-form-item label="AppId" prop="social_login_appid" class="!mb-4">
                            <el-input
                                v-model="formData.social_login_appid"
                                placeholder="请输入聚合登录AppId"
                                @blur="() => handleInputChange('social_login_appid', formData.social_login_appid)"
                            />
                        </el-form-item>
                        <el-form-item label="AppKey" prop="social_login_appkey">
                            <el-input
                                v-model="formData.social_login_appkey"
                                placeholder="请输入聚合登录AppKey"
                                @blur="() => handleInputChange('social_login_appkey', formData.social_login_appkey)"
                            />
                        </el-form-item>
                        <div class="form-tips">请到 <el-link type="primary" href="https://u.xiaobaixuan.com/" target="_blank">小白轩聚合登录</el-link> 申请AppId和AppKey</div>
                    </div>
                </el-form-item>

                <el-form-item label="登录方式" v-if="switchState.social_login">
                    <div>
                        <el-checkbox
                            v-model="switchState.social_login_qq_enable"
                            @change="(val: any) => handleSwitchChange('social_login_qq_enable', val)"
                        >
                            QQ登录
                        </el-checkbox>
                        <el-checkbox
                            v-model="switchState.social_login_wx_enable"
                            @change="(val: any) => handleSwitchChange('social_login_wx_enable', val)"
                        >
                            微信登录
                        </el-checkbox>
                        <el-checkbox
                            v-model="switchState.social_login_alipay_enable"
                            @change="(val: any) => handleSwitchChange('social_login_alipay_enable', val)"
                        >
                            支付宝登录
                        </el-checkbox>
                        <el-checkbox
                            v-model="switchState.social_login_baidu_enable"
                            @change="(val: any) => handleSwitchChange('social_login_baidu_enable', val)"
                        >
                            百度登录
                        </el-checkbox>
                        <el-checkbox
                            v-model="switchState.social_login_microsoft_enable"
                            @change="(val: any) => handleSwitchChange('social_login_microsoft_enable', val)"
                        >
                            微软登录
                        </el-checkbox>
                    </div>
                </el-form-item>

                <el-form-item label="微信开放平台">
                    <div>
                        <a href="https://open.weixin.qq.com/" target="_blank">
                            <el-button type="primary" link class="underline">
                                前往微信开放平台
                            </el-button>
                        </a>

                        <div class="form-tips">
                            1、在各渠道使用微信授权登录时，强烈建议配置微信开放平台<br />
                            2、微信开放平台关联公众号、小程序和 APP 后，可实现各端用户账号统一，识别买家唯一微信身份<br />
                            3、没有配置微信开放平台，同一微信号会生成多个用户，配置微信开放平台后已生成的用户账号无法合并
                        </div>
                    </div>
                </el-form-item>
            </el-card>

            <!-- 快捷入口 -->
            <el-card shadow="never" class="!border-none mt-4">
                <template #header>
                    <div class="card-header">
                        <el-icon><Link /></el-icon>
                        <span>快捷入口</span>
                    </div>
                </template>
                <el-space wrap>
                    <el-button type="primary" plain @click="$router.push('/setting/email')">
                        <el-icon><Message /></el-icon>
                        邮箱配置
                    </el-button>
                    <el-button type="primary" plain @click="$router.push('/message/short_letter')">
                        <el-icon><Chat-Dot-Round /></el-icon>
                        短信配置
                    </el-button>
                    <el-button type="primary" plain @click="$router.push('/app/message/notice')">
                        <el-icon><Bell /></el-icon>
                        通知设置
                    </el-button>
                </el-space>
            </el-card>
        </el-form>
    </div>
</template>

<script lang="ts" setup name="loginRegister">
import type { FormInstance, FormRules } from 'element-plus'
import { ElMessage } from 'element-plus'
import { User, Message, Link, InfoFilled, ChatDotRound, Bell } from '@element-plus/icons-vue'

import type { LoginSetup } from '@/api/setting/user'
import { getLogin } from '@/api/setting/user'
import request from '@/utils/request'

const formRef = ref<FormInstance>()

interface FormData extends LoginSetup {
    register_open: number
    register_verify_type: string
    merchant_apply_open: number
    merchant_apply_verify_type: string
    distributor_apply_verify_type: string
    withdraw_verify_type: string
    email_notify_open: number
    sms_notify_open: number
    social_login: number
    social_login_appid: string
    social_login_appkey: string
    social_login_qq_enable: number
    social_login_wx_enable: number
    social_login_alipay_enable: number
    social_login_baidu_enable: number
    social_login_microsoft_enable: number
}

// 表单数据（用于非开关字段和提交）
const formData = reactive<FormData>({
    login_way: [],
    coerce_mobile: 0,
    login_agreement: 0,
    third_auth: 0,
    wechat_auth: 0,
    qq_auth: 0,
    social_login: 0,
    social_login_appid: '',
    social_login_appkey: '',
    social_login_qq_enable: 0,
    social_login_wx_enable: 0,
    social_login_alipay_enable: 0,
    social_login_baidu_enable: 0,
    social_login_microsoft_enable: 0,
    register_open: 1,
    register_verify_type: 'email',
    merchant_apply_open: 1,
    merchant_apply_verify_type: 'email',
    distributor_apply_verify_type: 'email',
    withdraw_verify_type: 'email',
    email_notify_open: 0,
    sms_notify_open: 1
})

// 开关状态（使用布尔值，参考邮箱配置页面）
const switchState = reactive({
    coerce_mobile: false,
    login_agreement: false,
    third_auth: false,
    wechat_auth: false,
    social_login: false,
    social_login_qq_enable: false,
    social_login_wx_enable: false,
    social_login_alipay_enable: false,
    social_login_baidu_enable: false,
    social_login_microsoft_enable: false,
    register_open: true,
    merchant_apply_open: true,
    email_notify_open: false,
    sms_notify_open: true
})

const rules = reactive<FormRules>({
    loginWay: [
        {
            required: true,
            validator: (rule: any, value: any, callback: any) => {
                const loginWay = formData.login_way.join('').length
                if (loginWay === 0) {
                    callback(new Error('登录方式至少选择一项！'))
                } else {
                    if (formData.login_way !== '') {
                        if (!formRef.value) return
                        formRef.value.validateField('checkPass')
                    }
                    callback()
                }
            },
            trigger: 'change'
        }
    ],
    coerce_mobile: [{ required: true, trigger: 'blur' }],
    login_agreement: [{ required: true, trigger: 'blur' }],
    third_auth: [{ required: true, trigger: 'blur' }]
})

// 标记是否正在从服务器加载数据
const isLoadingData = ref(false)

const getData = async () => {
    try {
        isLoadingData.value = true
        const data = await getLogin()
        // 逐个字段更新，保持响应式
        // 注意：不能使用 || 运算符，因为 0 会被判断为 falsy
        formData.login_way = data.login_way || []
        formData.coerce_mobile = Number(data.coerce_mobile ?? 0)
        formData.login_agreement = Number(data.login_agreement ?? 0)
        formData.third_auth = Number(data.third_auth ?? 0)
        formData.wechat_auth = Number(data.wechat_auth ?? 0)
        formData.qq_auth = Number(data.qq_auth ?? 0)
        formData.social_login = Number(data.social_login ?? 0)
        formData.social_login_appid = data.social_login_appid || ''
        formData.social_login_appkey = data.social_login_appkey || ''
        formData.social_login_qq_enable = Number(data.social_login_qq_enable ?? 0)
        formData.social_login_wx_enable = Number(data.social_login_wx_enable ?? 0)
        formData.social_login_alipay_enable = Number(data.social_login_alipay_enable ?? 0)
        formData.social_login_baidu_enable = Number(data.social_login_baidu_enable ?? 0)
        formData.social_login_microsoft_enable = Number(data.social_login_microsoft_enable ?? 0)
        formData.register_open = Number(data.register_open ?? 1)
        formData.register_verify_type = data.register_verify_type || 'email'
        formData.merchant_apply_open = Number(data.merchant_apply_open ?? 1)
        formData.merchant_apply_verify_type = data.merchant_apply_verify_type || 'email'
        formData.distributor_apply_verify_type = data.distributor_apply_verify_type || 'email'
        formData.withdraw_verify_type = data.withdraw_verify_type || 'email'
        formData.email_notify_open = Number(data.email_notify_open ?? 0)
        formData.sms_notify_open = Number(data.sms_notify_open ?? 1)
        
        // 同步开关状态（数字转布尔值）
        switchState.coerce_mobile = formData.coerce_mobile === 1
        switchState.login_agreement = formData.login_agreement === 1
        switchState.third_auth = formData.third_auth === 1
        switchState.wechat_auth = formData.wechat_auth === 1
        switchState.social_login = formData.social_login === 1
        switchState.social_login_qq_enable = formData.social_login_qq_enable === 1
        switchState.social_login_wx_enable = formData.social_login_wx_enable === 1
        switchState.social_login_alipay_enable = formData.social_login_alipay_enable === 1
        switchState.social_login_baidu_enable = formData.social_login_baidu_enable === 1
        switchState.social_login_microsoft_enable = formData.social_login_microsoft_enable === 1
        switchState.register_open = formData.register_open === 1
        switchState.merchant_apply_open = formData.merchant_apply_open === 1
        switchState.email_notify_open = formData.email_notify_open === 1
        switchState.sms_notify_open = formData.sms_notify_open === 1
        
        console.log('表单数据已更新:', formData)
        console.log('开关状态已更新:', switchState)
    } catch (error) {
        console.error('获取配置失败', error)
    } finally {
        // 延迟重置标记，确保 Vue 的更新周期完成
        setTimeout(() => {
            isLoadingData.value = false
        }, 100)
    }
}

// 全局开关提交锁 - 防止任何开关同时提交
const isAnySwitchSubmitting = ref(false)

const handleSwitchChange = async (key: string, value: any) => {
    if (isLoadingData.value) {
        console.log('正在加载数据，忽略开关变化:', key, value)
        return
    }
    
    if (isAnySwitchSubmitting.value) {
        console.log('有其他开关正在提交，忽略:', key)
        return
    }
    
    console.log('开关变化:', key, value)
    
    try {
        isAnySwitchSubmitting.value = true
        
        const numValue = value === true ? 1 : (value === false ? 0 : value)
        
        formData[key as keyof FormData] = numValue as any
        
        const params: any = {
            [key]: numValue
        }
        
        await request.post({
            url: '/setting.user.user/setRegisterConfig',
            params,
            requestOptions: {
                isOpenRetry: false,
                show: false
            }
        })
        ElMessage.success('保存成功')
    } catch (error: any) {
        getData()
    } finally {
        setTimeout(() => {
            isAnySwitchSubmitting.value = false
        }, 300)
    }
}

const handleLoginWayChange = async (value: any) => {
    if (isLoadingData.value) {
        console.log('正在加载数据，忽略登录方式变化:', value)
        return
    }
    
    if (isAnySwitchSubmitting.value) {
        console.log('有其他开关正在提交，忽略登录方式变化')
        return
    }
    
    if (value.length === 0) {
        ElMessage.warning('登录方式至少选择一项！')
        getData()
        return
    }
    
    console.log('登录方式变化:', value)
    
    try {
        isAnySwitchSubmitting.value = true
        
        const params: any = {
            login_way: value
        }
        
        await request.post({
            url: '/setting.user.user/setRegisterConfig',
            params,
            requestOptions: {
                isOpenRetry: false,
                show: false
            }
        })
        ElMessage.success('保存成功')
    } catch (error: any) {
        getData()
    } finally {
        setTimeout(() => {
            isAnySwitchSubmitting.value = false
        }, 300)
    }
}

const handleRadioChange = async (key: string, value: any) => {
    if (isLoadingData.value) {
        console.log('正在加载数据，忽略radio变化:', key, value)
        return
    }
    
    if (isAnySwitchSubmitting.value) {
        console.log('有其他开关正在提交，忽略radio变化:', key)
        return
    }
    
    console.log('radio变化:', key, value)
    
    try {
        isAnySwitchSubmitting.value = true
        
        const params: any = {
            [key]: value
        }
        
        await request.post({
            url: '/setting.user.user/setRegisterConfig',
            params,
            requestOptions: {
                isOpenRetry: false,
                show: false
            }
        })
        ElMessage.success('保存成功')
    } catch (error: any) {
        getData()
    } finally {
        setTimeout(() => {
            isAnySwitchSubmitting.value = false
        }, 300)
    }
}

const handleInputChange = async (key: string, value: any) => {
    if (isLoadingData.value) {
        console.log('正在加载数据，忽略输入变化:', key, value)
        return
    }
    
    if (isAnySwitchSubmitting.value) {
        console.log('有其他开关正在提交，忽略输入变化:', key)
        return
    }
    
    console.log('输入变化:', key, value)
    
    try {
        isAnySwitchSubmitting.value = true
        
        const params: any = {
            [key]: value
        }
        
        await request.post({
            url: '/setting.user.user/setRegisterConfig',
            params,
            requestOptions: {
                isOpenRetry: false,
                show: false
            }
        })
    } catch (error: any) {
        getData()
    } finally {
        setTimeout(() => {
            isAnySwitchSubmitting.value = false
        }, 300)
    }
}

onMounted(() => {
    getData()
})
</script>

<style lang="scss" scoped>
.card-header {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    gap: 8px;
    font-weight: bold;
    font-size: 16px;
}

.form-tips {
    font-size: 12px;
    color: #909399;
    margin-top: 5px;
    line-height: 1.5;
    display: flex;
    align-items: flex-start;
    gap: 4px;
}
</style>
