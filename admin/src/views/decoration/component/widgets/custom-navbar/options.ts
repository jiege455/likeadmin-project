export default () => ({
    title: '自定义导航',
    name: 'custom-navbar',
    icon: 'el-icon-ArrowLeft',
    content: {
        enabled: 1,
        title: '页面标题', // 标题
        bg_color: '#ffffff', // 背景颜色
        text_color: '#000000', // 文字颜色
        show_back: 1, // 是否显示返回按钮
        fixed: 1 // 是否固定顶部
    },
    styles: {
        padding_top: 0,
        padding_bottom: 0
    }
})
