import { createSSRApp } from 'vue'
import App from './App.vue'
import plugins from './plugins'
import router from './router'
import './styles/index.scss'
import 'vant/lib/index.css' // 引入 Vant 样式
import Vant from 'vant' // 引入 Vant 组件库
import { setupMixin } from './mixins'

export function createApp() {
    const app = createSSRApp(App)
    setupMixin(app)
    app.use(plugins)
    app.use(router)
    app.use(Vant) // 全局注册 Vant
    return {
        app
    }
}
