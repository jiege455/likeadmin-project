<!--
  开发者公众号：杰哥网络科技。
  qq2711793818 杰哥
  功能说明：商家中心入口组件预览
-->
<template>
    <div class="merchant-center-preview" :style="containerStyle">
        <div class="preview-switch mb-2 px-3">
            <el-radio-group v-model="previewStatus" size="small">
                <el-radio-button :label="0">未申请</el-radio-button>
                <el-radio-button :label="1">审核中</el-radio-button>
                <el-radio-button :label="2">已拒绝</el-radio-button>
                <el-radio-button :label="3">已通过</el-radio-button>
            </el-radio-group>
        </div>
        
        <div class="preview-container bg-white rounded-xl overflow-hidden" :style="{ marginLeft: `${styles.padding_horizontal || 12}px`, marginRight: `${styles.padding_horizontal || 12}px`, borderRadius: `${styles.border_radius || 12}px` }">
            <template v-if="previewStatus === 0">
                <div class="header px-4 py-3 flex items-center justify-between" :style="{ backgroundColor: styles.primary_color || '#EF4444' }">
                    <div class="flex items-center">
                        <el-icon size="20" color="#fff"><Shop /></el-icon>
                        <span class="text-white font-bold ml-2">成为商家</span>
                    </div>
                    <div class="apply-btn px-3 py-1 bg-white rounded-full text-xs" :style="{ color: styles.primary_color || '#EF4444' }">
                        立即申请
                    </div>
                </div>
                <div class="content p-4">
                    <div class="grid grid-cols-4 gap-2 text-center">
                        <div class="flex flex-col items-center">
                            <el-icon :size="24" :color="styles.primary_color || '#EF4444'"><Edit /></el-icon>
                            <span class="text-xs text-gray-500 mt-1">发布文章</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <el-icon :size="24" :color="styles.primary_color || '#EF4444'"><Ticket /></el-icon>
                            <span class="text-xs text-gray-500 mt-1">优惠券</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <el-icon :size="24" :color="styles.primary_color || '#EF4444'"><User /></el-icon>
                            <span class="text-xs text-gray-500 mt-1">粉丝管理</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <el-icon :size="24" :color="styles.primary_color || '#EF4444'"><Money /></el-icon>
                            <span class="text-xs text-gray-500 mt-1">收益提现</span>
                        </div>
                    </div>
                </div>
            </template>
            
            <template v-else-if="previewStatus === 1">
                <div class="p-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center bg-orange-100">
                            <el-icon :size="24" color="#f97316"><Clock /></el-icon>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="text-base font-bold text-gray-800">商家申请审核中</div>
                            <div class="text-xs text-gray-400 mt-1">请耐心等待管理员审核</div>
                        </div>
                        <div class="px-3 py-1 bg-orange-100 rounded-full text-xs text-orange-500">审核中</div>
                    </div>
                </div>
            </template>
            
            <template v-else-if="previewStatus === 2">
                <div class="p-4">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center bg-red-100">
                            <el-icon :size="24" color="#ef4444"><CircleClose /></el-icon>
                        </div>
                        <div class="ml-3 flex-1">
                            <div class="text-base font-bold text-gray-800">申请被拒绝</div>
                            <div class="text-xs text-red-400 mt-1">请重新提交申请</div>
                        </div>
                        <div class="px-3 py-1 rounded-full text-xs text-white" :style="{ backgroundColor: styles.primary_color || '#EF4444' }">
                            重新申请
                        </div>
                    </div>
                </div>
            </template>
            
            <template v-else-if="previewStatus === 3">
                <div class="header px-4 py-3 flex items-center justify-between" :style="{ backgroundColor: styles.primary_color || '#EF4444' }">
                    <div class="flex items-center">
                        <el-icon size="20" color="#fff"><Shop /></el-icon>
                        <span class="text-white font-bold ml-2">商家中心</span>
                    </div>
                    <el-icon size="16" color="#fff"><ArrowRight /></el-icon>
                </div>
                <div class="content p-4">
                    <div class="grid grid-cols-4 gap-2 text-center">
                        <div class="flex flex-col items-center">
                            <div class="text-lg font-bold text-red-500">¥0.00</div>
                            <span class="text-xs text-gray-400 mt-1">账户余额</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="text-lg font-bold text-blue-500">0</div>
                            <span class="text-xs text-gray-400 mt-1">订单数</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="text-lg font-bold text-green-500">0</div>
                            <span class="text-xs text-gray-400 mt-1">粉丝数</span>
                        </div>
                        <div class="flex flex-col items-center">
                            <div class="text-lg font-bold text-orange-500">0</div>
                            <span class="text-xs text-gray-400 mt-1">文章数</span>
                        </div>
                    </div>
                </div>
                <div class="quick-actions flex border-t border-gray-100">
                    <div class="flex-1 py-3 flex items-center justify-center">
                        <el-icon size="18" :color="styles.primary_color || '#EF4444'"><Edit /></el-icon>
                        <span class="text-xs text-gray-600 ml-1">发布文章</span>
                    </div>
                    <div class="flex-1 py-3 flex items-center justify-center border-l border-r border-gray-100">
                        <el-icon size="18" :color="styles.primary_color || '#EF4444'"><List /></el-icon>
                        <span class="text-xs text-gray-600 ml-1">订单管理</span>
                    </div>
                    <div class="flex-1 py-3 flex items-center justify-center">
                        <el-icon size="18" :color="styles.primary_color || '#EF4444'"><Money /></el-icon>
                        <span class="text-xs text-gray-600 ml-1">提现</span>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>

<script lang="ts" setup>
import { ref, computed } from 'vue'
import { Shop, ArrowRight, Edit, List, Money, Ticket, User, Clock, CircleClose } from '@element-plus/icons-vue'

const props = defineProps({
    content: {
        type: Object,
        default: () => ({})
    },
    styles: {
        type: Object,
        default: () => ({
            margin_top: 0,
            margin_bottom: 10,
            padding_horizontal: 12,
            border_radius: 12,
            primary_color: '#EF4444'
        })
    }
})

const previewStatus = ref(3)

const containerStyle = computed(() => ({
    marginTop: `${props.styles.margin_top || 0}px`,
    marginBottom: `${props.styles.margin_bottom || 10}px`
}))
</script>

<style lang="scss" scoped>
.merchant-center-preview {
    background: #f5f5f5;
    padding-top: 10px;
    padding-bottom: 10px;
}

.preview-switch {
    display: flex;
    justify-content: center;
}
</style>
