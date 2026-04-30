export default () => ({
    title: '领券中心',
    name: 'coupon-center',
    icon: 'el-icon-Ticket',
    content: {
        enabled: 1,
        // 可以添加配置项，比如是否显示“我的优惠券”入口
        show_my_coupon: 1,
        // 列表样式：1-单列，2-双列 (预留)
        style: 1,
        // 主题色
        theme_color: '#e1251b'
    },
    styles: {
        padding_top: 10,
        padding_bottom: 10,
        bg_color: '#f5f5f5'
    }
})
