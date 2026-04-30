import request from '@/utils/request'

/**
 * 获取邮箱配置
 */
export function getConfig() {
  return request.get({ url: '/setting.email/config' })
}

/**
 * 保存邮箱配置
 */
export function setConfig(params: any) {
  return request.post({ url: '/setting.email/setConfig', params })
}

/**
 * 获取开关配置
 */
export function getSwitchConfig() {
  return request.get({ url: '/setting.systemSwitch/config' })
}

/**
 * 保存开关配置
 */
export function setSwitchConfig(params: any) {
  return request.post({ url: '/setting.systemSwitch/config', params })
}

/**
 * 发送测试邮件
 */
export function sendTest(params: any) {
  return request.post({ url: '/setting.email/test', params })
}
