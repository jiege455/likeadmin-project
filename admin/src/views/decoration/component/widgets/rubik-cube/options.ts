export default () => ({
    title: '图片魔方',
    name: 'rubik-cube',
    content: {
        enabled: 1,
        style: 1, // 1: 一行两个 2: 一行三个
        data: [
            {
                image: '',
                link: {
                    path: '',
                    name: '',
                    type: '',
                    query: {}
                }
            },
            {
                image: '',
                link: {
                    path: '',
                    name: '',
                    type: '',
                    query: {}
                }
            }
        ]
    },
    styles: {
        bg_color: '#ffffff',
        margin: 10,
        padding: 10,
        radius: 0
    }
})
