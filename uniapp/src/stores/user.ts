import { getUserCenter } from '@/api/user'
import { TOKEN_KEY } from '@/enums/constantEnums'
import cache from '@/utils/cache'
import { defineStore } from 'pinia'

// 用户信息接口定义
interface UserInfo {
    id?: number
    nickname?: string
    avatar?: string
    mobile?: string
    [key: string]: any
}

interface UserSate {
    userInfo: UserInfo
    token: string | null
    temToken: string | null
}
export const useUserStore = defineStore({
    id: 'userStore',
    state: (): UserSate => ({
        userInfo: {},
        token: cache.get(TOKEN_KEY) || null,
        temToken: null
    }),
    getters: {
        isLogin: (state) => !!state.token
    },
    actions: {
        async getUser(): Promise<UserInfo> {
            // 如果没有 token，直接返回，避免无效请求
            if (!this.token && !this.temToken) {
                this.userInfo = {}
                return Promise.resolve({})
            }
            try {
                const data = await getUserCenter({
                    token: this.token || this.temToken
                })
                this.userInfo = data
                return Promise.resolve(data)
            } catch (e) {
                // 如果获取用户信息失败（如 token 过期），清空登录状态
                this.logout()
                return Promise.resolve({})
            }
        },
        login(token: string) {
            this.token = token
            cache.set(TOKEN_KEY, token)
            uni.removeStorageSync('invite_code')
        },
        // 设置token（用于token刷新）
        // 开发者：杰哥网络科技 qq2711793818 杰哥
        setToken(token: string) {
            this.token = token
            cache.set(TOKEN_KEY, token)
        },
        logout() {
            this.token = ''
            this.userInfo = {}
            cache.remove(TOKEN_KEY)
        }
    }
})
