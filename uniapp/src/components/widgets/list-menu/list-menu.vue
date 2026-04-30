<template>
    <div
        class="list-menu bg-white rounded-lg overflow-hidden"
        :style="{ marginBottom: `${styles.margin_bottom}px` }"
        v-if="content.enabled"
    >
        <div
            v-for="(item, index) in showList"
            :key="index"
            class="menu-item flex items-center justify-between px-4 py-3 border-b border-gray-50 last:border-0 active:bg-gray-50 transition-colors"
            @click="handleClick(item)"
        >
            <div class="flex items-center">
                <image
                    v-if="item.image"
                    :src="getImageUrl(item.image)"
                    class="w-6 h-6 mr-3"
                    mode="aspectFit"
                />
                <span class="text-sm text-gray-800">{{ item.name }}</span>
            </div>
            <div class="flex items-center">
                <span class="text-gray-400">
                    <uni-icons type="right" size="14" color="#999" />
                </span>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue'
import { useAppStore } from '@/stores/app'
import { navigateTo } from '@/utils/util'

const props = defineProps({
    content: {
        type: Object,
        default: () => ({})
    },
    styles: {
        type: Object,
        default: () => ({})
    }
})

const appStore = useAppStore()

const getImageUrl = (url: string) => {
    return appStore.getImageUrl(url)
}

const showList = computed(() => {
    return props.content.data?.filter((item: any) => item.is_show == '1') || []
})

const handleClick = (item: any) => {
    if (item.link) {
        navigateTo(item.link)
    }
}
</script>

<style lang="scss" scoped>
.list-menu {
    .menu-item {
        &:active {
            background-color: #f9fafb;
        }
    }
}
</style>
