export default () => ({
    title: '列表菜单',
    name: 'list-menu',
    icon: 'el-icon-Menu',
    content: {
        enabled: 1,
        style: 1, // 1=列表模式
        data: [
            {
                image: '',
                name: '菜单名称',
                link: {},
                is_show: '1'
            }
        ]
    },
    styles: {
        margin_bottom: 10
    }
})
