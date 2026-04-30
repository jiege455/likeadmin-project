<template>
    <uni-nav title="收款账户">
        <template #right>
            <view class="w-10 h-full flex items-center justify-center" @click="toAdd">
                <u-icon name="plus" size="20"></u-icon>
            </view>
        </template>
    </uni-nav>

    <view class="withdraw-account min-h-screen bg-f5">
        <view class="list p-3">
            <view
                class="item bg-white rounded-xl mb-3 p-4 shadow-sm"
                v-for="(item, index) in list"
                :key="index"
                @click="handleSelect(item)"
            >
                <view class="flex items-center justify-between">
                    <view class="flex items-center">
                        <view v-if="item.qrcode" class="w-10 h-10 rounded-lg overflow-hidden">
                            <image
                                :src="item.qrcode"
                                mode="aspectFill"
                                class="w-full h-full"
                            ></image>
                        </view>
                        <view
                            v-else
                            class="w-10 h-10 rounded-full flex items-center justify-center"
                            :class="
                                item.type == 2
                                    ? 'bg-blue-100'
                                    : item.type == 1
                                    ? 'bg-green-100'
                                    : 'bg-orange-100'
                            "
                        >
                            <u-icon
                                :name="
                                    item.type == 2
                                        ? 'zhifubao'
                                        : item.type == 1
                                        ? 'weixin-fill'
                                        : 'integral-fill'
                                "
                                size="20"
                                :color="
                                    item.type == 2
                                        ? '#1677FF'
                                        : item.type == 1
                                        ? '#07C160'
                                        : '#FF8C00'
                                "
                            ></u-icon>
                        </view>
                        <view class="ml-3">
                            <view class="font-bold">{{ item.type_text }}</view>
                            <view class="text-sm text-gray-500 mt-1">{{ item.account_mask }}</view>
                            <view class="text-xs text-gray-400">{{ item.real_name }}</view>
                        </view>
                    </view>
                    <view class="flex items-center">
                        <view
                            v-if="item.is_default"
                            class="text-xs px-2 py-1 rounded-full mr-2"
                            :style="{
                                backgroundColor: themeStore.primaryColor + '20',
                                color: themeStore.primaryColor
                            }"
                            >默认</view
                        >
                        <u-icon
                            name="more-dot-fill"
                            size="20"
                            color="#999"
                            @click.stop="showActions(item)"
                        ></u-icon>
                    </view>
                </view>
                <view
                    v-if="item.type == 3"
                    class="mt-2 pt-2 border-t border-gray-100 text-xs text-gray-400"
                >
                    {{ item.bank_name }} {{ item.bank_branch }}
                </view>
            </view>
            <u-empty v-if="list.length === 0" mode="list" text="暂无收款账户">
                <template #button>
                    <view class="mt-4">
                        <u-button
                            text="添加账户"
                            @click="toAdd"
                            :color="themeStore.primaryColor"
                            size="small"
                        ></u-button>
                    </view>
                </template>
            </u-empty>
        </view>

        <!-- 添加按钮 -->
        <view class="fixed bottom-0 left-0 right-0 p-4 bg-white" v-if="list.length > 0">
            <u-button
                text="添加收款账户"
                @click="toAdd"
                :color="themeStore.primaryColor"
            ></u-button>
        </view>

        <!-- 操作弹窗 -->
        <u-action-sheet
            :list="actionList"
            v-model="showActionSheet"
            @click="handleAction"
        ></u-action-sheet>
    </view>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue'
import request from '@/utils/request'
import { onShow, onLoad } from '@dcloudio/uni-app'
import { useThemeStore } from '@/stores/theme'
import { safeNavigateBack } from '@/utils/util'

const themeStore = useThemeStore()
const themeStyle = computed(() => `--color-primary: ${themeStore.primaryColor};`)

const list = ref<any[]>([])
const currentItem = ref<any>(null)
const showActionSheet = ref(false)
const merchantId = ref(0)

const actionList = [
    { text: '设为默认', subText: '' },
    { text: '编辑', subText: '' },
    { text: '删除', subText: '', color: '#FF4D4F' }
]

const getList = async () => {
    try {
        const res = await request.get({
            url: '/withdraw.account/lists',
            data: { merchant_id: merchantId.value }
        })
        list.value = res.lists || []
    } catch (e) {
        list.value = []
    }
}

const toAdd = () => {
    uni.navigateTo({
        url: `/packages/pages/user/withdraw_account_add?merchant_id=${merchantId.value}`
    })
}

const showActions = (item: any) => {
    currentItem.value = item
    showActionSheet.value = true
}

const handleAction = async (index: number) => {
    if (!currentItem.value) return

    if (index === 0) {
        try {
            await request.post({
                url: '/withdraw.account/setDefault',
                data: { id: currentItem.value.id, merchant_id: merchantId.value }
            })
            uni.$u.toast('设置成功')
            getList()
        } catch (e) {}
    } else if (index === 1) {
        uni.navigateTo({
            url: `/packages/pages/user/withdraw_account_add?id=${currentItem.value.id}&merchant_id=${merchantId.value}`
        })
    } else if (index === 2) {
        uni.showModal({
            title: '提示',
            content: '确定要删除该收款账户吗？',
            success: async (res) => {
                if (res.confirm) {
                    try {
                        await request.post({
                            url: '/withdraw.account/delete',
                            data: { id: currentItem.value.id, merchant_id: merchantId.value }
                        })
                        uni.$u.toast('删除成功')
                        getList()
                    } catch (e) {}
                }
            }
        })
    }
}

const handleSelect = (item: any) => {
    const pages = getCurrentPages()
    const prevPage = pages[pages.length - 2] as any
    if (prevPage && prevPage.$vm) {
        prevPage.$vm.selectedAccount = item
    }
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

const goBack = () => {
    safeNavigateBack({ defaultUrl: '/pages/index/index' })
}

onLoad((options: any) => {
    if (options.merchant_id) {
        merchantId.value = parseInt(options.merchant_id)
    }
})

onShow(() => {
    uni.setNavigationBarTitle({ title: '收款账户' })
    getList()
    themeStore.getTheme()
})
</script>

<style lang="scss" scoped>
.withdraw-account {
    background-color: #f5f5f5;
    padding-bottom: 80px;
}
.bg-f5 {
    background-color: #f5f5f5;
}
</style>
