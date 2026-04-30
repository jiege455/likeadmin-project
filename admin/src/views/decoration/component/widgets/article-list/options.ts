export default () => ({
    title: '文章列表',
    name: 'article-list',
    icon: 'el-icon-DocumentCopy',
    content: {
        title: '最新资讯',
        limit: 5,
        showPrice: true,
        showImage: true
    },
    styles: {}
})
export interface optionsType {
    title: string
    limit: number
    showPrice: boolean
    showImage: boolean
}
