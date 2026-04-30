<!-- 
  系统开关配置页面 - 已整合到登录注册配置
  开发者：杰哥网络科技
  QQ: 2711793818
-->
<template>
  <div>
    <el-card class="!border-none" shadow="never">
      <el-result
        icon="warning"
        title="功能已整合"
        sub-title="系统开关配置已整合到登录注册配置页面，请前往该页面进行设置"
      >
        <template #extra>
          <el-button type="primary" @click="$router.push('/setting/user/login_register')">
            前往登录注册配置
          </el-button>
        </template>
      </el-result>
    </el-card>
  </div>
</template>

<script lang="ts" setup name="systemSwitch">
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

onMounted(() => {
  // 自动跳转到登录注册配置页面
  router.replace('/setting/user/login_register')
})
</script>
