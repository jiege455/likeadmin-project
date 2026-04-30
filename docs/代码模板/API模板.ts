/**
 * {{title}}相关接口
 * @author 杰哥
 * @date {{date}}
 */
import request from '@/utils/request'

// 类型定义
export interface {{name}}Params {
    id?: number
    keyword?: string
    status?: number
    page_no?: number
    page_size?: number
}

export interface {{name}}Info {
    id: number
    // TODO: 填写字段
    status: number
    create_time: string
}

// 获取{{title}}列表
export function get{{name}}Lists(params: {{name}}Params) {
    return request.get({ url: '/{{module}}.{{nameLower}}/lists', data: params })
}

// 获取{{title}}详情
export function get{{name}}Detail(id: number) {
    return request.get({ url: '/{{module}}.{{nameLower}}/detail', data: { id } })
}

// 添加{{title}}
export function add{{name}}(data: {{name}}Params) {
    return request.post({ url: '/{{module}}.{{nameLower}}/add', data })
}

// 编辑{{title}}
export function edit{{name}}(data: {{name}}Params) {
    return request.post({ url: '/{{module}}.{{nameLower}}/edit', data })
}

// 删除{{title}}
export function delete{{name}}(id: number) {
    return request.post({ url: '/{{module}}.{{nameLower}}/delete', data: { id } })
}

// 切换{{title}}状态
export function change{{name}}Status(id: number) {
    return request.post({ url: '/{{module}}.{{nameLower}}/status', data: { id } })
}
