export default () => ({
    title: '文章详情',
    name: 'article-detail',
    icon: 'el-icon-Document',
    content: {
        placeholderTitle: '文章详情预览',
        style: 'simple'
    },
    styles: {}
})
export interface optionsType {
    placeholderTitle: string
    style: 'simple' | 'detail'
}
