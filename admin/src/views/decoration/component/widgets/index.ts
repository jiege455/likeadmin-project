const widgets: Record<string, any> = import.meta.glob('./**/index.ts', { eager: true })
interface Widget {
    attr?: any
    content: any
    options: any
    name?: string
    title?: string
    icon?: string
    [key: string]: any
}

const exportWidgets: Record<string, Widget> = {}
Object.keys(widgets).forEach((key) => {
    // 排除自身
    if (key === './index.ts') return
    
    const widgetName = key.replace(/^\.\/([\w-]+).*/gi, '$1')
    const def = widgets[key]?.default || {}
    
    // 兼容 options 为函数的情况（likeadmin 原生规范）
    if (def.options) {
        const opts = typeof def.options === 'function' ? def.options() : def.options
        // 使用 opts 补充属性，但 def (组件) 优先级更高，防止 content 被覆盖
        // 同时保留 options 函数以便后续调用
        exportWidgets[widgetName] = { ...opts, ...def, options: def.options }
    } else {
        exportWidgets[widgetName] = def
    }
})

export default exportWidgets
