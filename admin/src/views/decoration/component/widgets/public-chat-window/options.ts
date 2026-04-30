export default () => ({
    title: '公共聊天',
    name: 'public-chat-window',
    icon: 'el-icon-ChatDotSquare',
    content: {
        title: '公共频道',
        showOnlineCount: true
    },
    styles: {}
})
export interface optionsType {
    title: string
    showOnlineCount: boolean
}
