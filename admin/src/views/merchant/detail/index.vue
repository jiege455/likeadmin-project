<template>
    <div class="merchant-detail">
        <el-card class="!border-none mb-4" shadow="never">
            <div class="flex items-center mb-4">
                <el-button @click="goBack" text>
                    <el-icon><ArrowLeft /></el-icon>
                    返回列表
                </el-button>
            </div>
        </el-card>

        <div class="flex gap-4">
            <!-- 左侧商户信息 -->
            <el-card class="!border-none flex-1" shadow="never" v-loading="loading">
                <template #header>
                    <div class="flex items-center justify-between">
                        <span class="font-medium">商户信息</span>
                        <el-tag :type="statusType">{{ merchant.status_text }}</el-tag>
                    </div>
                </template>
                
                <div class="flex items-center mb-6">
                    <el-avatar :size="80" :src="merchant.logo || merchant.avatar">
                        <el-icon :size="40"><User /></el-icon>
                    </el-avatar>
                    <div class="ml-4">
                        <div class="text-xl font-bold">{{ merchant.name }}</div>
                        <div class="text-gray-400 text-sm mt-1">{{ merchant.mobile || merchant.user_mobile || '-' }}</div>
                    </div>
                </div>

                <el-descriptions :column="2" border>
                    <el-descriptions-item label="商户ID">{{ merchant.id }}</el-descriptions-item>
                    <el-descriptions-item label="关联用户">{{ merchant.nickname || '-' }}</el-descriptions-item>
                    <el-descriptions-item label="联系电话">{{ merchant.mobile || merchant.user_mobile || '-' }}</el-descriptions-item>
                    <el-descriptions-item label="微信号">{{ merchant.wechat || '-' }}</el-descriptions-item>
                    <el-descriptions-item label="入驻时间">{{ merchant.create_time }}</el-descriptions-item>
                    <el-descriptions-item label="审核时间">{{ merchant.audit_time || '-' }}</el-descriptions-item>
                    <el-descriptions-item label="拒绝原因" v-if="merchant.status === 2">
                        <span class="text-red-500">{{ merchant.audit_reason }}</span>
                    </el-descriptions-item>
                    <el-descriptions-item label="店铺简介" :span="2">{{ merchant.intro || '-' }}</el-descriptions-item>
                </el-descriptions>
            </el-card>

            <!-- 右侧数据统计 -->
            <el-card class="!border-none w-80" shadow="never" v-loading="loading">
                <template #header>
                    <span class="font-medium">数据统计</span>
                </template>
                
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-2xl font-bold text-blue-500">{{ stats.article_count }}</div>
                        <div class="text-sm text-gray-500 mt-1">文章数</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-2xl font-bold text-green-500">{{ stats.order_count }}</div>
                        <div class="text-sm text-gray-500 mt-1">订单数</div>
                    </div>
                    <div class="text-center p-4 bg-orange-50 rounded-lg">
                        <div class="text-2xl font-bold text-orange-500">{{ stats.fans_count }}</div>
                        <div class="text-sm text-gray-500 mt-1">粉丝数</div>
                    </div>
                    <div class="text-center p-4 bg-purple-50 rounded-lg">
                        <div class="text-2xl font-bold text-purple-500">¥{{ stats.balance }}</div>
                        <div class="text-sm text-gray-500 mt-1">账户余额</div>
                    </div>
                </div>

                <div class="mt-4 space-y-3">
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                        <span class="text-gray-500">今日收入</span>
                        <span class="text-red-500 font-bold">¥{{ stats.today_income }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                        <span class="text-gray-500">累计收入</span>
                        <span class="text-red-500 font-bold">¥{{ stats.total_income }}</span>
                    </div>
                    <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                        <span class="text-gray-500">已提现</span>
                        <span class="text-green-500 font-bold">¥{{ stats.withdraw_amount }}</span>
                    </div>
                </div>
            </el-card>
        </div>

        <!-- 文章列表 -->
        <el-card class="!border-none mt-4" shadow="never">
            <el-tabs v-model="activeTab">
                <el-tab-pane label="文章列表" name="articles">
                    <el-table :data="articles" v-loading="articleLoading">
                        <el-table-column label="ID" prop="id" width="80" />
                        <el-table-column label="标题" prop="title" min-width="200" />
                        <el-table-column label="价格" width="100">
                            <template #default="{ row }">
                                <span v-if="row.price > 0" class="text-red-500">¥{{ row.price }}</span>
                                <span v-else class="text-green-500">免费</span>
                            </template>
                        </el-table-column>
                        <el-table-column label="购买数" prop="buy_count" width="80" align="center" />
                        <el-table-column label="收入" width="100" align="right">
                            <template #default="{ row }">
                                <span class="text-red-500">¥{{ row.income || '0.00' }}</span>
                            </template>
                        </el-table-column>
                        <el-table-column label="状态" width="80">
                            <template #default="{ row }">
                                <el-tag :type="row.audit_status === 1 ? 'success' : 'warning'">
                                    {{ row.audit_status === 1 ? '已通过' : '待审核' }}
                                </el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column label="发布时间" prop="create_time" width="160" />
                    </el-table>
                    <div class="flex justify-end mt-4">
                        <el-pagination
                            v-model:current-page="articlePage"
                            :page-size="10"
                            :total="articleTotal"
                            layout="total, prev, pager, next"
                            @current-change="getArticles"
                        />
                    </div>
                </el-tab-pane>
                
                <el-tab-pane label="订单列表" name="orders">
                    <el-table :data="orders" v-loading="orderLoading">
                        <el-table-column label="订单号" prop="order_sn" width="200" />
                        <el-table-column label="用户" min-width="120">
                            <template #default="{ row }">
                                <div class="flex items-center">
                                    <el-avatar :size="24" :src="row.avatar" />
                                    <span class="ml-2">{{ row.nickname }}</span>
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column label="文章" prop="article_title" min-width="150" />
                        <el-table-column label="金额" width="100" align="right">
                            <template #default="{ row }">
                                <span class="text-red-500">¥{{ row.order_amount }}</span>
                            </template>
                        </el-table-column>
                        <el-table-column label="状态" width="80">
                            <template #default="{ row }">
                                <el-tag :type="row.pay_status === 1 ? 'success' : 'warning'">
                                    {{ row.pay_status === 1 ? '已支付' : '待支付' }}
                                </el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column label="支付时间" prop="pay_time" width="160" />
                    </el-table>
                    <div class="flex justify-end mt-4">
                        <el-pagination
                            v-model:current-page="orderPage"
                            :page-size="10"
                            :total="orderTotal"
                            layout="total, prev, pager, next"
                            @current-change="getOrders"
                        />
                    </div>
                </el-tab-pane>
            </el-tabs>
        </el-card>
    </div>
</template>

<script setup lang="ts">
import { merchantDetail, merchantStatistics, merchantArticles, merchantOrders } from '@/api/merchant'
import { ArrowLeft, User } from '@element-plus/icons-vue'
import { useRoute, useRouter } from 'vue-router'

const route = useRoute()
const router = useRouter()
const merchantId = route.query.id as string

const loading = ref(false)
const articleLoading = ref(false)
const orderLoading = ref(false)

const merchant = ref<any>({})
const stats = ref<any>({
    article_count: 0,
    order_count: 0,
    fans_count: 0,
    balance: '0.00',
    today_income: '0.00',
    total_income: '0.00',
    withdraw_amount: '0.00'
})

const articles = ref<any[]>([])
const articlePage = ref(1)
const articleTotal = ref(0)

const orders = ref<any[]>([])
const orderPage = ref(1)
const orderTotal = ref(0)

const activeTab = ref('articles')

const statusType = computed(() => {
    const types: Record<number, string> = {
        0: 'warning',
        1: 'success',
        2: 'danger',
        3: 'info'
    }
    return types[merchant.value.status] || 'info'
})

const goBack = () => {
    router.push('/merchant/list')
}

const getDetail = async () => {
    loading.value = true
    try {
        const res = await merchantDetail({ id: merchantId })
        merchant.value = res
    } finally {
        loading.value = false
    }
}

const getStatistics = async () => {
    const res = await merchantStatistics({ id: merchantId })
    stats.value = res
}

const getArticles = async () => {
    articleLoading.value = true
    try {
        const res = await merchantArticles({ id: merchantId, page: articlePage.value, limit: 10 })
        articles.value = res.lists || []
        articleTotal.value = res.count || 0
    } finally {
        articleLoading.value = false
    }
}

const getOrders = async () => {
    orderLoading.value = true
    try {
        const res = await merchantOrders({ id: merchantId, page: orderPage.value, limit: 10 })
        orders.value = res.lists || []
        orderTotal.value = res.count || 0
    } finally {
        orderLoading.value = false
    }
}

onMounted(() => {
    getDetail()
    getStatistics()
    getArticles()
    getOrders()
})
</script>
