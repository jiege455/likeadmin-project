import { defineStore } from 'pinia'
import { getConfig } from '@/api/app'

interface AppSate {
    config: Record<string, any>
    isLoadingConfig: boolean
    isConfigLoaded: boolean
}
export const useAppStore = defineStore({
    id: 'appStore',
    state: (): AppSate => ({
        config: {
            domain: ''
        },
        isLoadingConfig: false,
        isConfigLoaded: false
    }),
    getters: {
        getWebsiteConfig: (state) => state.config.website || {},
        getLoginConfig: (state) => state.config.login || {},
        getTabbarConfig: (state) => state.config.tabbar || [],
        getStyleConfig: (state) => state.config.style || {},
        getH5Config: (state) => state.config.webPage || {},
        getCopyrightConfig: (state) => state.config.copyright || []
    },
    actions: {
        getImageUrl(url: string) {
            if (!url) return ''
            if (url.indexOf('http') === 0 || url.indexOf('data:image') === 0) return url
            return `${this.config.domain}${url}`
        },
        async getConfig() {
            if (this.isLoadingConfig) return

            this.isLoadingConfig = true
            try {
                const data = await getConfig()
                this.config = data
                this.isConfigLoaded = true
            } finally {
                this.isLoadingConfig = false
            }
        }
    }
})
