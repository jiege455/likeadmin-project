export default () => ({
    title: '商家资料列表',
    name: 'merchant-content-list',
    icon: 'el-icon-Files',
    content: {
        enabled: 1,
        style: 'single', // single=单列, double=双列
        show_tags: 1, // 显示标签
        show_price: 1 // 显示价格
    },
    styles: {
        padding_top: 10,
        padding_bottom: 10
    }
})
