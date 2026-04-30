<template>
    <div class="pc-banner" :style="styles">
        <div class="banner-image w-full h-full" :style="{ backgroundColor: styles.background_color }">
            <decoration-img
                width="100%"
                :height="height"
                :src="getImage"
                fit="contain"
            />
        </div>
    </div>
</template>
<script lang="ts" setup>
import type { PropType } from 'vue'

import DecorationImg from '../../decoration-img.vue'
import type options from './options'

type OptionsType = ReturnType<typeof options>
const props = defineProps({
    content: {
        type: Object as PropType<OptionsType['content']>,
        default: () => ({})
    },
    styles: {
        type: Object as PropType<OptionsType['styles']>,
        default: () => ({})
    },
    height: {
        type: String,
        default: '340px'
    }
})

const getImage = computed(() => {
    if (Array.isArray(props.content.data) && props.content.data.length > 0) {
        return props.content.data[0].image || ''
    }
    return ''
})
</script>

<style lang="scss" scoped>
.pc-banner {
    width: 100%;
    overflow: hidden;
}
</style>
