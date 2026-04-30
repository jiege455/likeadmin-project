export default () => ({
    title: '充值面板',
    name: 'recharge-panel',
    icon: 'el-icon-Money',
    content: {
        enabled: 1,
        // 充值金额选项
        amounts: [
            { value: 30, text: '30元' },
            { value: 50, text: '50元' },
            { value: 100, text: '100元' },
            { value: 200, text: '200元' },
            { value: 300, text: '300元' },
            { value: 500, text: '500元' }
        ],
        show_custom_amount: 1, // 是否允许自定义金额
        btn_color: '#e1251b', // 确认按钮颜色
        btn_text: '确认充值', // 按钮文字
        notice: '1. 仅用于查看作者推荐数据\n2. 充值金额可提现到捆绑银行卡，最少充值和提现金额为30元' // 充值说明
    },
    styles: {
        padding_top: 10,
        padding_bottom: 10
    }
})
