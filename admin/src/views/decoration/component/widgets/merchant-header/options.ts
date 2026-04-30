export default () => ({
    title: '商家信息头',
    name: 'merchant-header',
    icon: 'el-icon-UserFilled',
    content: {
        enabled: 1,
        bg_type: 1, // 背景类型：1-纯色，2-图片
        bg_color: '#e1251b', // 红色背景
        bg_image: '', // 背景图片
        show_stats: 1, // 是否显示统计
        show_desc: 1, // 是否显示简介
        show_buttons: 1, // 是否显示按钮组
        // 按钮显示控制
        show_wechat: 1,
        show_share: 1,
        show_chat: 1,
        show_complain: 1,
        // 切换商家链接
        switch_link: {
            path: '/pages/merchant/attention', // 默认跳转到我的关注
            name: '我的关注',
            type: 'page'
        }
    },
    styles: {}
})
