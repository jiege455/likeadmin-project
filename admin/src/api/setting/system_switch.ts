import request from '@/utils/request'

/**
 * 获取系统开关配置
 */
export function getSwitchConfig() {
  return request.get({ url: '/setting.systemSwitch/config' })
}

/**
 * 保存系统开关配置
 */
export function setSwitchConfig(params: any) {
  return request.post({ url: '/setting.systemSwitch/setConfig', params })
}
