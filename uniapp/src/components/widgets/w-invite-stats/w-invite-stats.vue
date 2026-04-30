<template>
    <view
        v-if="stats && stats.total_invite_count !== undefined"
        class="w-invite-stats px-3"
        :style="{
            paddingTop: `${styles.padding_top}px`,
            paddingBottom: `${styles.padding_bottom}px`
        }"
    >
        <view
            class="stats-card rounded-lg p-5 flex justify-between items-center shadow-md"
            :style="{
                backgroundColor: content.bg_color || '#e1251b',
                color: content.text_color || '#ffffff'
            }"
        >
            <view class="flex-1 text-center border-r border-white/20">
                <view class="text-sm opacity-80 mb-1">今日邀请 (人)</view>
                <view class="text-3xl font-bold">{{ stats.today_invite_count || 0 }}</view>
            </view>
            <view class="flex-1 text-center">
                <view class="text-sm opacity-80 mb-1">累计邀请 (人)</view>
                <view class="text-3xl font-bold">{{ stats.total_invite_count || 0 }}</view>
            </view>
        </view>
    </view>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { getDistributionIndex } from '@/api/distribution'

defineProps({
    content: {
        type: Object,
        default: () => ({})
    },
    styles: {
        type: Object,
        default: () => ({})
    }
})

const stats = ref<any>(null)

const getStats = async () => {
    try {
        const res = await getDistributionIndex()
        stats.value = res || null
    } catch (e: any) {
        if (e?.code !== 20001) {
            console.error(e)
        }
        stats.value = null
    }
}

onMounted(() => {
    getStats()
})
</script>

<style lang="scss" scoped></style>
