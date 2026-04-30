export default () => ({
    title: '钱包头部',
    name: 'wallet-header',
    icon: 'el-icon-Wallet',
    content: {
        enabled: 1,
        bg_color: '#e1251b', // 背景颜色
        text_color: '#ffffff', // 文字颜色
        show_balance: 1, // 显示余额
        show_detail_btn: 1 // 显示明细按钮
    },
    styles: {
        padding_top: 0,
        padding_bottom: 0
    }
})
