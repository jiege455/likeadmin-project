export default () => ({
    title: '我的钱包',
    name: 'wallet-card',
    icon: 'el-icon-Wallet',
    content: {
        wallet_link: {
            path: '/packages/pages/user_wallet/user_wallet',
            name: '我的钱包',
            type: 'shop'
        },
        commission_link: {
            path: '/packages/pages/distribution/distribution',
            name: '分销中心',
            type: 'shop'
        }
    },
    styles: {
        root_bg_color: '#ffffff',
        balance_color: '#333333',
        label_color: '#999999',
        border_radius: 8,
        margin_top: 0,
        margin_bottom: 10
    }
})
