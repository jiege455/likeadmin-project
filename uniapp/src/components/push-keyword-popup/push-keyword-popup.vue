<template>
    <u-popup v-model="show" mode="bottom" border-radius="20" :closeable="true" @close="handleClose">
        <view class="keyword-popup p-4">
            <view class="text-center font-bold text-lg mb-4">
                推送关键词设置
            </view>
            
            <view class="text-gray-500 text-sm mb-4 text-center">
                设置关键词后，当该商家发布包含关键词的文章时会通知您
            </view>

            <view class="add-box flex mb-4">
                <u-input
                    v-model="newKeyword"
                    placeholder="请输入关键词（最多20字）"
                    border="surround"
                    class="flex-1 mr-2"
                    :maxlength="20"
                />
                <u-button
                    type="primary"
                    size="small"
                    :custom-style="{ backgroundColor: themeColor, color: '#fff', border: 'none' }"
                    @click="handleAdd"
                >
                    添加
                </u-button>
            </view>

            <view class="keyword-list" v-if="keywordList.length > 0">
                <view
                    class="keyword-item flex items-center justify-between p-3 bg-gray-50 rounded-lg mb-2"
                    v-for="(item, index) in keywordList"
                    :key="item.id"
                >
                    <view class="flex items-center flex-1">
                        <view
                            class="keyword-tag px-2 py-1 rounded text-sm"
                            :class="item.is_enable ? 'bg-blue-100 text-blue-600' : 'bg-gray-200 text-gray-400'"
                        >
                            {{ item.keyword }}
                        </view>
                        <view class="text-xs text-gray-400 ml-2" v-if="!item.is_enable">
                            已禁用
                        </view>
                    </view>
                    <view class="flex items-center">
                        <u-switch
                            v-model="item.is_enable"
                            :active-value="1"
                            :inactive-value="0"
                            size="20"
                            :active-color="themeColor"
                            @change="handleToggle(item)"
                        ></u-switch>
                        <u-icon
                            name="trash"
                            size="18"
                            color="#999"
                            class="ml-3"
                            @click="handleDelete(item)"
                        ></u-icon>
                    </view>
                </view>
            </view>

            <u-empty v-else text="暂无关键词，请添加" icon="info-circle" iconSize="60" :show="true"></u-empty>

            <view class="tips text-xs text-gray-400 mt-4">
                <view>* 每个商家最多设置10个关键词</view>
                <view>* 关键词匹配文章标题或内容时会推送通知</view>
            </view>
        </view>
    </u-popup>
</template>

<script setup lang="ts">
/**
 * 推送关键词设置弹窗组件
 * 开发者：杰哥网络科技 qq2711793818 杰哥
 */
import { ref, watch, computed } from 'vue'
import { getPushKeywordLists, addPushKeyword, deletePushKeyword, togglePushKeyword } from '@/api/push'
import { useThemeStore } from '@/stores/theme'

const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false
    },
    merchantId: {
        type: [Number, String],
        default: 0
    },
    merchantName: {
        type: String,
        default: ''
    }
})

const emit = defineEmits(['update:modelValue', 'refresh'])

const themeStore = useThemeStore()
const themeColor = computed(() => themeStore.primaryColor)

const show = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val)
})

const keywordList = ref<any[]>([])
const newKeyword = ref('')

const getList = async () => {
    const merchantId = Number(props.merchantId)
    if (!merchantId) return
    try {
        const res = await getPushKeywordLists({ merchant_id: merchantId })
        keywordList.value = res || []
    } catch (e) {
        console.error('获取关键词列表失败', e)
    }
}

const handleAdd = async () => {
    if (!newKeyword.value.trim()) {
        uni.showToast({ title: '请输入关键词', icon: 'none' })
        return
    }
    
    const merchantId = Number(props.merchantId)
    if (!merchantId) {
        uni.showToast({ title: '商家信息错误', icon: 'none' })
        return
    }
    
    try {
        await addPushKeyword({
            merchant_id: merchantId,
            keyword: newKeyword.value.trim()
        })
        uni.showToast({ title: '添加成功', icon: 'success' })
        newKeyword.value = ''
        getList()
    } catch (e: any) {
        const errMsg = typeof e === 'string' ? e : (e.msg || '添加失败')
        uni.showToast({ title: errMsg, icon: 'none' })
    }
}

const handleToggle = async (item: any) => {
    try {
        await togglePushKeyword({ id: item.id })
        uni.showToast({ title: '操作成功', icon: 'success' })
    } catch (e: any) {
        item.is_enable = item.is_enable ? 0 : 1
        const errMsg = typeof e === 'string' ? e : (e.msg || '操作失败')
        uni.showToast({ title: errMsg, icon: 'none' })
    }
}

const handleDelete = (item: any) => {
    uni.showModal({
        title: '提示',
        content: `确定删除关键词"${item.keyword}"吗？`,
        success: async (res) => {
            if (res.confirm) {
                try {
                    await deletePushKeyword({ id: item.id })
                    uni.showToast({ title: '删除成功', icon: 'success' })
                    getList()
                } catch (e: any) {
                    const errMsg = typeof e === 'string' ? e : (e.msg || '删除失败')
                    uni.showToast({ title: errMsg, icon: 'none' })
                }
            }
        }
    })
}

const handleClose = () => {
    emit('update:modelValue', false)
}

watch(() => props.modelValue, (val) => {
    if (val && props.merchantId) {
        getList()
    }
})
</script>

<style lang="scss" scoped>
.keyword-popup {
    max-height: 70vh;
    overflow-y: auto;
}

.keyword-tag {
    max-width: 200rpx;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
</style>
