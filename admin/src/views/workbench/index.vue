<template>
    <div class="workbench">
        <div class="pending-section mb-4" v-if="workbenchData.pending && workbenchData.pending.length > 0">
            <el-card class="!border-none" shadow="never">
                <template #header>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <el-icon class="mr-2" :size="18"><Bell /></el-icon>
                            <span class="card-title">待处理事项</span>
                            <el-badge :value="workbenchData.pending.length" class="ml-2" type="danger" />
                        </div>
                        <router-link to="/pending_approval">
                            <el-button type="primary" size="small">查看全部</el-button>
                        </router-link>
                    </div>
                </template>
                <div class="flex flex-wrap gap-4">
                    <router-link 
                        v-for="(item, index) in workbenchData.pending" 
                        :key="index"
                        :to="item.url"
                        class="pending-item"
                    >
                        <el-card 
                            class="pending-card" 
                            :body-style="{ padding: '20px' }"
                            shadow="hover"
                        >
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <el-icon :size="32" :class="'text-' + item.type">
                                        <Shop v-if="item.icon === 'el-icon-Shop'" />
                                        <Money v-else-if="item.icon === 'el-icon-Money'" />
                                        <Wallet v-else-if="item.icon === 'el-icon-Wallet'" />
                                        <UserFilled v-else-if="item.icon === 'el-icon-UserFilled'" />
                                        <Document v-else-if="item.icon === 'el-icon-Document'" />
                                        <Bell v-else />
                                    </el-icon>
                                    <div class="ml-3">
                                        <div class="text-base font-medium">{{ item.name }}</div>
                                        <div class="text-tx-secondary text-xs mt-1">点击立即处理</div>
                                    </div>
                                </div>
                                <el-badge :value="item.count" :type="item.type" :max="99" />
                            </div>
                        </el-card>
                    </router-link>
                </div>
            </el-card>
        </div>
        <div class="lg:flex">
            <el-card class="!border-none mb-4 lg:mr-4 lg:w-[350px]" shadow="never">
                <template #header>
                    <span class="card-title">版本信息</span>
                </template>
                <div>
                    <div class="flex leading-9">
                        <div class="w-20">平台名称</div>
                        <span> {{ workbenchData.version.name }}</span>
                    </div>
                    <div class="flex leading-9">
                        <div class="w-20">当前版本</div>
                        <span> {{ workbenchData.version.version }}</span>
                    </div>
                    <div class="flex leading-9">
                        <div class="w-20">获取渠道</div>
                        <div>
                            <a :href="workbenchData.version.channel.website" target="_blank">
                                <el-button type="success" size="small">官网</el-button>
                            </a>
                            <a
                                class="ml-3"
                                :href="workbenchData.version.channel.gitee"
                                target="_blank"
                            >
                                <el-button type="danger" size="small">Gitee</el-button>
                            </a>
                        </div>
                    </div>
                </div>
            </el-card>
            <el-card class="!border-none mb-4 flex-1" shadow="never">
                <template #header>
                    <div>
                        <span class="card-title">今日数据</span>
                        <span class="text-tx-secondary text-xs ml-4">
                            更新时间：{{ workbenchData.today.time }}
                        </span>
                    </div>
                </template>

                <div class="flex flex-wrap">
                    <div class="w-1/2 md:w-1/4">
                        <div class="leading-10">销售额</div>
                        <div class="text-6xl">{{ workbenchData.today.today_sales }}</div>
                        <div class="text-tx-secondary text-xs">
                            总：{{ workbenchData.today.total_sales }}
                        </div>
                    </div>
                    <div class="w-1/2 md:w-1/4">
                        <div class="leading-10">成交订单</div>
                        <div class="text-6xl">{{ workbenchData.today.order_num }}</div>
                        <div class="text-tx-secondary text-xs">
                            总：{{ workbenchData.today.order_sum }}
                        </div>
                    </div>
                    <div class="w-1/2 md:w-1/4">
                        <div class="leading-10">新增用户</div>
                        <div class="text-6xl">{{ workbenchData.today.today_new_user }}</div>
                        <div class="text-tx-secondary text-xs">
                            总：{{ workbenchData.today.total_new_user }}
                        </div>
                    </div>
                    <div class="w-1/2 md:w-1/4">
                        <div class="leading-10">新增访问量</div>
                        <div class="text-6xl">{{ workbenchData.today.today_visitor }}</div>
                        <div class="text-tx-secondary text-xs">
                            总：{{ workbenchData.today.total_visitor }}
                        </div>
                    </div>
                </div>
            </el-card>
        </div>
        <div class="function mb-4">
            <el-card class="flex-1 !border-none" shadow="never">
                <template #header>
                    <span>常用功能</span>
                </template>
                <div class="flex flex-wrap">
                    <div
                        v-for="item in workbenchData.menu"
                        class="md:w-[12.5%] w-1/4 flex flex-col items-center"
                        :key="item"
                    >
                        <router-link :to="item.url" class="mb-3 flex flex-col items-center">
                            <image-contain width="40px" height="40px" :src="item?.image" />
                            <div class="mt-2">{{ item.name }}</div>
                        </router-link>
                    </div>
                </div>
            </el-card>
        </div>
        <div class="lg:flex gap-4">
            <el-card class="!border-none mb-4 lg:mb-0 w-full lg:w-2/3" shadow="never">
                <template #header>
                    <span>访问量趋势图</span>
                </template>
                <div>
                    <v-charts
                        ref="visitorChart"
                        style="height: 350px"
                        :option="workbenchData.visitorOption"
                        :autoresize="true"
                    />
                </div>
            </el-card>
            <el-card class="!border-none w-full lg:w-1/3" shadow="never">
                <template #header>
                    <span>销售额趋势图</span>
                </template>
                <div>
                    <v-charts
                        ref="saleChart"
                        style="height: 350px"
                        :option="workbenchData.saleOption"
                        :autoresize="true"
                    />
                </div>
            </el-card>
        </div>
    </div>
</template>

<script lang="ts" setup name="workbench">
import vCharts from 'vue-echarts'
import { Bell, Shop, Money, Wallet, UserFilled, Document } from '@element-plus/icons-vue'

import { getWorkbench } from '@/api/app'
import useSettingStore from '@/stores/modules/setting'
import { useComponentRef } from '@/utils/getExposeType'
import { calcColor } from '@/utils/util'

const settingStore = useSettingStore()
const saleChart = useComponentRef(vCharts)
const visitorChart = useComponentRef(vCharts)

watch(
    () => settingStore.theme,
    () => {
        updateColor()
    }
)

// 表单数据
const workbenchData: any = reactive({
    version: {
        version: '', // 版本号
        website: '', // 官网
        based: '',
        channel: {
            gitee: '',
            website: ''
        }
    },
    support: [],
    today: {}, // 今日数据
    menu: [], // 常用功能
    visitor: [], // 访问量
    article: [], // 文章阅读量
    pending: [], // 待处理事项

    visitorOption: {
        xAxis: {
            type: 'category',
            data: []
        },
        yAxis: {
            type: 'value'
        },
        legend: {
            data: ['访问量']
        },
        tooltip: {
            trigger: 'axis'
        },
        series: [
            {
                name: '访问量',
                data: [],
                type: 'line',
                smooth: true,
                color: settingStore.theme,
                lineStyle: {
                    color: settingStore.theme,
                    width: 2
                },
                areaStyle: {
                    color: {
                        type: 'linear',
                        x: 0,
                        y: 0,
                        x2: 0,
                        y2: 1,
                        colorStops: [
                            {
                                offset: 0,
                                color: settingStore.theme
                            },
                            {
                                offset: 1,
                                color: settingStore.theme
                            }
                        ]
                    },
                    opacity: 0.1
                }
            }
        ]
    },

    saleOption: {
        xAxis: {
            type: 'category',
            data: []
        },
        yAxis: {
            type: 'value',
            name: '单位（万）'
        },
        tooltip: {
            trigger: 'axis'
        },
        series: [
            {
                name: '销售量',
                data: [],
                type: 'bar',
                showBackground: true,
                backgroundStyle: {
                    color: 'rgba(180, 180, 180, 0.2)',
                    borderRadius: [10, 10, 0, 0]
                },
                barWidth: '40%',
                itemStyle: {
                    borderRadius: [10, 10, 0, 0],
                    color: {
                        type: 'linear',
                        x: 0,
                        y: 0,
                        x2: 0,
                        y2: 1,
                        colorStops: [
                            {
                                offset: 0,
                                color: calcColor(settingStore.theme, 0.7)
                            },
                            {
                                offset: 1,
                                color: settingStore.theme
                            }
                        ]
                    }
                }
            }
        ]
    }
})

// 获取工作台主页数据
const getData = () => {
    getWorkbench()
        .then((res: any) => {
            workbenchData.version = res.version
            workbenchData.today = res.today
            workbenchData.menu = res.menu
            workbenchData.visitor = res.visitor
            workbenchData.support = res.support
            workbenchData.pending = res.pending || []

            // 清空echarts 数据
            workbenchData.visitorOption.xAxis.data = []
            workbenchData.visitorOption.series[0].data = []
            workbenchData.saleOption.xAxis.data = []
            workbenchData.saleOption.series[0].data = []

            // 写入从后台拿来的数据
            res.visitor?.date?.reverse().forEach((item: any) => {
                workbenchData.visitorOption.xAxis.data.push(item)
            })
            if (res.visitor?.list?.[0]?.data) {
                res.visitor.list[0].data.forEach((item: any) => {
                    workbenchData.visitorOption.series[0].data.push(item)
                })
            }
            
            res.sale?.date?.reverse().forEach((item: any) => {
                workbenchData.saleOption.xAxis.data.push(item)
            })
            
            if (res.sale?.list?.[0]?.data) {
                res.sale.list[0].data.forEach((item: any) => {
                    if (item <= 50) {
                        item = {
                            value: item,
                            itemStyle: {
                                color: {
                                    type: 'linear',
                                    x: 0,
                                    y: 0,
                                    x2: 0,
                                    y2: 1,
                                    colorStops: [
                                        {
                                            offset: 0,
                                            color: calcColor('#ff8729', 0.7)
                                        },
                                        {
                                            offset: 1,
                                            color: '#ff8729'
                                        }
                                    ]
                                }
                            }
                        }
                    }
                    workbenchData.saleOption.series[0].data.push(item)
                })
            }
        })
        .catch((err: any) => {
            // 错误处理
        })
}

const updateColor = () => {
    workbenchData.visitorOption.series[0].color = settingStore.theme
    workbenchData.visitorOption.series[0].lineStyle.color = settingStore.theme
    workbenchData.visitorOption.series[0].areaStyle.color.colorStops = [
        {
            offset: 0,
            color: settingStore.theme
        },
        {
            offset: 1,
            color: settingStore.theme
        }
    ]
    workbenchData.saleOption.series[0].itemStyle.color.colorStops = [
        {
            offset: 0,
            color: calcColor(settingStore.theme, 0.7)
        },
        {
            offset: 1,
            color: settingStore.theme
        }
    ]

    saleChart.value?.clear()
    visitorChart.value?.clear()
    saleChart.value?.setOption(workbenchData.saleOption)
    visitorChart.value?.setOption(workbenchData.visitorOption)
}

onMounted(() => {
    getData()
})
</script>

<style lang="scss" scoped>
.pending-section {
    :deep(.el-card__header) {
        padding: 15px 20px;
        border-bottom: 1px solid #ebeef5;
    }
}

.pending-item {
    display: block;
    text-decoration: none;
    min-width: 280px;
    flex: 1;
    
    .pending-card {
        cursor: pointer;
        transition: all 0.3s;
        border: 1px solid #ebeef5;
        
        &:hover {
            border-color: var(--el-color-primary);
            transform: translateY(-2px);
        }
    }
}

.text-warning {
    color: var(--el-color-warning);
}

.text-danger {
    color: var(--el-color-danger);
}

.text-success {
    color: var(--el-color-success);
}

.text-primary {
    color: var(--el-color-primary);
}
</style>
