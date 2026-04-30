<!-- 
  邮箱配置页面
  开发者：杰哥网络科技
  QQ: 2711793818
-->
<template>
  <div>
    <el-card class="!border-none" shadow="never">
      <el-alert
        type="warning"
        title="温馨提示：配置邮箱服务用于发送注册验证码、订单通知等邮件"
        :closable="false"
        show-icon
      ></el-alert>
    </el-card>

    <el-card class="!border-none mt-4" shadow="never">
      <el-form ref="formRef" :model="formData" :rules="formRules" label-width="120px">
        <el-divider content-position="left">SMTP 服务器配置</el-divider>

        <el-form-item label="SMTP 服务器" prop="smtp_server">
          <el-input v-model="formData.smtp_server" placeholder="例如：smtp.qq.com" clearable />
          <div class="form-tip">QQ 邮箱：smtp.qq.com | 163 邮箱：smtp.163.com</div>
        </el-form-item>

        <el-form-item label="端口" prop="port">
          <el-input-number v-model="formData.port" :min="1" :max="65535" placeholder="465" />
          <div class="form-tip">SSL 加密：465 | TLS 加密：587 | 无加密：25</div>
        </el-form-item>

        <el-form-item label="加密方式" prop="encrypt">
          <el-radio-group v-model="formData.encrypt">
            <el-radio label="ssl">SSL（推荐）</el-radio>
            <el-radio label="tls">TLS</el-radio>
            <el-radio label="">无加密</el-radio>
          </el-radio-group>
        </el-form-item>

        <el-divider content-position="left">发件人配置</el-divider>

        <el-form-item label="发件人账号" prop="username">
          <el-input v-model="formData.username" placeholder="您的邮箱账号" clearable />
          <div class="form-tip">例如：your@qq.com</div>
        </el-form-item>

        <el-form-item label="授权密码" prop="password">
          <el-input
            v-model="formData.password"
            type="password"
            placeholder="邮箱授权码（非登录密码）"
            clearable
            show-password
          />
          <div class="form-tip">
            QQ 邮箱：登录邮箱→设置→账户→生成授权码 | 
            <el-link type="primary" href="https://mail.qq.com" target="_blank">QQ 邮箱设置</el-link>
          </div>
        </el-form-item>

        <el-form-item label="发件人名称" prop="from_name">
          <el-input v-model="formData.from_name" placeholder="系统通知" clearable />
          <div class="form-tip">显示在邮件中的发件人名称</div>
        </el-form-item>

        <el-form-item label="发件人邮箱" prop="from_email">
          <el-input v-model="formData.from_email" placeholder="默认使用发件人账号" clearable />
          <div class="form-tip">可选，不填则使用发件人账号</div>
        </el-form-item>

        <el-form-item label="管理员邮箱" prop="admin_email">
          <el-input v-model="formData.admin_email" placeholder="接收系统通知的邮箱" clearable />
          <div class="form-tip">用于接收系统通知、错误报告等</div>
        </el-form-item>

        <el-form-item label="网站地址" prop="website_url">
          <el-input v-model="formData.website_url" placeholder="例如：https://www.example.com" clearable />
          <div class="form-tip">用于邮件中显示网站链接，可不填</div>
        </el-form-item>

        <el-divider content-position="left">发送测试</el-divider>

        <el-form-item label="测试邮箱">
          <el-input v-model="testEmail" placeholder="请输入测试邮箱地址" clearable style="width: 300px;" />
          <el-button type="primary" @click="sendTestEmail" :loading="sendingTest" class="ml-2">
            发送测试邮件
          </el-button>
        </el-form-item>

        <el-form-item>
          <el-button type="primary" @click="saveConfig" :loading="loading">保存配置</el-button>
          <el-button @click="resetForm">重置</el-button>
        </el-form-item>
      </el-form>
    </el-card>

    <el-card class="!border-none mt-4" shadow="never">
      <template #header>
        <div class="card-header">
          <span>邮件通知开关</span>
        </div>
      </template>
      <el-form label-width="180px">
        <el-form-item label="邮件通知总开关">
          <el-switch
            v-model="emailNotifyOpen"
            @change="updateSwitch('email_notify_open', $event, 'system')"
            active-text="开启"
            inactive-text="关闭"
          />
          <div class="form-tip">
            关闭后所有邮件通知将停止发送（包括注册验证码、订单通知等）
          </div>
        </el-form-item>
      </el-form>
    </el-card>

    <el-card class="!border-none mt-4" shadow="never">
      <template #header>
        <div class="card-header">
          <span>独立场景开关控制</span>
          <el-tag type="info" size="small">可单独控制每个场景的邮件通知</el-tag>
        </div>
      </template>
      <el-form label-width="200px">
        <el-divider content-position="left">商家相关</el-divider>
        
        <el-form-item label="商家入驻申请通知管理员">
          <el-switch
            v-model="switchConfig.merchant_apply_admin_notify"
            @change="updateSwitch('merchant_apply_admin_notify', $event, 'email_switch')"
            active-text="开启"
            inactive-text="关闭"
          />
          <div class="form-tip">用户提交商家入驻申请时通知管理员</div>
        </el-form-item>

        <el-form-item label="商家入驻审核结果通知">
          <el-switch
            v-model="switchConfig.merchant_audit_notify"
            @change="updateSwitch('merchant_audit_notify', $event, 'email_switch')"
            active-text="开启"
            inactive-text="关闭"
          />
          <div class="form-tip">商家入驻审核通过/拒绝后通知申请人</div>
        </el-form-item>

        <el-form-item label="订单支付成功通知商家">
          <el-switch
            v-model="switchConfig.order_notify"
            @change="updateSwitch('order_notify', $event, 'email_switch')"
            active-text="开启"
            inactive-text="关闭"
          />
          <div class="form-tip">用户购买文章支付成功后通知商家（需商家绑定邮箱）</div>
        </el-form-item>

        <el-form-item label="提现审核结果通知商家">
          <el-switch
            v-model="switchConfig.withdraw_notify"
            @change="updateSwitch('withdraw_notify', $event, 'email_switch')"
            active-text="开启"
            inactive-text="关闭"
          />
          <div class="form-tip">商家提现审核通过/拒绝后通知商家</div>
        </el-form-item>

        <el-divider content-position="left">分销相关</el-divider>
        
        <el-form-item label="分销申请通知管理员">
          <el-switch
            v-model="switchConfig.distribution_apply_notify"
            @change="updateSwitch('distribution_apply_notify', $event, 'email_switch')"
            active-text="开启"
            inactive-text="关闭"
          />
          <div class="form-tip">用户申请成为推广员时通知管理员</div>
        </el-form-item>

        <el-form-item label="分销审核结果通知用户">
          <el-switch
            v-model="switchConfig.distribution_audit_notify"
            @change="updateSwitch('distribution_audit_notify', $event, 'email_switch')"
            active-text="开启"
            inactive-text="关闭"
          />
          <div class="form-tip">分销申请审核通过/拒绝后通知申请人（需用户绑定邮箱）</div>
        </el-form-item>
      </el-form>
    </el-card>
  </div>
</template>

<script lang="ts" setup name="email">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage } from 'element-plus'
import { InfoFilled } from '@element-plus/icons-vue'
import request from '@/utils/request'

const formRef = ref()
const loading = ref(false)
const sendingTest = ref(false)
const testEmail = ref('')

const formData = reactive({
  smtp_server: '',
  port: 465,
  encrypt: 'ssl',
  username: '',
  password: '',
  from_name: '系统通知',
  from_email: '',
  admin_email: ''
})

const formRules = {
  smtp_server: [
    { required: true, message: '请输入 SMTP 服务器地址', trigger: 'blur' }
  ],
  username: [
    { required: true, message: '请输入发件人账号', trigger: 'blur' }
  ],
  password: [
    { required: true, message: '请输入授权密码', trigger: 'blur' }
  ]
}

const emailNotifyOpen = ref(false)

const switchConfig = reactive({
  merchant_apply_admin_notify: true,
  merchant_audit_notify: true,
  order_notify: true,
  withdraw_notify: true,
  distribution_apply_notify: true,
  distribution_audit_notify: true
})

const getConfigData = async () => {
  try {
    const res: any = await request.get({ url: '/setting.email/config' })
    if (res) {
      formData.smtp_server = res.smtp_server || ''
      formData.port = res.port || 465
      formData.encrypt = res.encrypt || 'ssl'
      formData.username = res.username || ''
      formData.password = res.password || ''
      formData.from_name = res.from_name || '系统通知'
      formData.from_email = res.from_email || ''
      formData.admin_email = res.admin_email || ''
    }
  } catch (error) {
    console.error('获取配置失败', error)
  }
}

const getSwitchData = async () => {
  try {
    const res: any = await request.get({ url: '/setting.systemSwitch/config' })
    if (res) {
      emailNotifyOpen.value = res.email_notify_open === '1' || res.email_notify_open === 1
    }
  } catch (error) {
    console.error('获取开关配置失败', error)
  }
}

const getEmailSwitchConfig = async () => {
  try {
    const res: any = await request.get({ url: '/setting.email/getSwitchConfig' })
    if (res) {
      Object.keys(switchConfig).forEach(key => {
        if (res[key] !== undefined) {
          switchConfig[key] = res[key] === '1' || res[key] === 1
        }
      })
    }
  } catch (error) {
    console.error('获取邮件开关配置失败', error)
  }
}

const saveConfig = async () => {
  if (!formRef.value) return
  
  await formRef.value.validate(async (valid: boolean) => {
    if (!valid) return
    
    loading.value = true
    try {
      await request.post({ 
        url: '/setting.email/setConfig', 
        params: formData,
        requestOptions: {
          isOpenRetry: false
        }
      })
      ElMessage.success('保存成功')
      getConfigData()
    } catch (error: any) {
      ElMessage.error(error?.message || '保存失败')
    } finally {
      loading.value = false
    }
  })
}

const resetForm = () => {
  if (!formRef.value) return
  formRef.value.resetFields()
  getConfigData()
}

const sendTestEmail = async () => {
  if (!testEmail.value) {
    ElMessage.warning('请输入测试邮箱地址')
    return
  }
  
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  if (!emailRegex.test(testEmail.value)) {
    ElMessage.warning('请输入正确的邮箱格式')
    return
  }
  
  sendingTest.value = true
  try {
    await request.post({ 
      url: '/setting.email/test', 
      params: { email: testEmail.value },
      requestOptions: {
        isOpenRetry: false
      }
    })
    ElMessage.success('测试邮件已发送，请检查邮箱')
  } catch (error: any) {
    ElMessage.error(error?.message || '发送失败')
  } finally {
    sendingTest.value = false
  }
}

const updateSwitch = async (key: string, value: any, type: string = 'email_switch') => {
  try {
    const params: any = {
      type: type,
      [key]: value === true ? '1' : (value === false ? '0' : value)
    }
    
    if (type === 'system') {
      delete params.type
      await request.post({ 
        url: '/setting.systemSwitch/setConfig', 
        params,
        requestOptions: {
          isOpenRetry: false
        }
      })
    } else {
      await request.post({ 
        url: '/setting.email/setSwitchConfig', 
        params,
        requestOptions: {
          isOpenRetry: false
        }
      })
    }
    ElMessage.success('保存成功')
  } catch (error: any) {
    ElMessage.error(error?.message || '保存失败')
    getSwitchData()
    getEmailSwitchConfig()
  }
}

onMounted(() => {
  getConfigData()
  getSwitchData()
  getEmailSwitchConfig()
})
</script>

<style lang="scss" scoped>
.form-tip {
  font-size: 12px;
  color: #909399;
  margin-top: 5px;
  line-height: 1.5;
  display: flex;
  align-items: center;
  gap: 4px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-weight: bold;
}

.ml-2 {
  margin-left: 10px;
}
</style>
