export default () => ({
    title: '推广员申请',
    name: 'promotion-apply-form',
    icon: 'el-icon-User',
    content: {
        title: '成为推广员',
        desc: '分享赚佣金，轻松月入过万',
        btnText: '立即申请',
        // 链接跳转
        link: {
            path: '',
            name: '',
            type: '',
            query: {}
        }
    },
    styles: {}
})
export interface optionsType {
    title: string
    desc: string
    btnText: string
    link: {
        path: string
        name: string
        type: string
        query?: any
    }
}
