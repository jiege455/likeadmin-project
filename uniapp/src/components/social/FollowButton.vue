<template>
    <view class="follow-btn" @click.stop="handleFollow">
        <u-button
            :type="isFollowed ? 'default' : 'primary'"
            size="mini"
            shape="circle"
            :plain="isFollowed"
        >
            <u-icon :name="isFollowed ? 'checkmark' : 'plus'" size="24" class="mr-1"></u-icon>
            {{ isFollowed ? '已关注' : '关注' }}
        </u-button>
    </view>
</template>

<script setup lang="ts">
import { ref, defineProps, defineEmits } from 'vue'

const props = defineProps({
    userId: { type: [String, Number], required: true },
    initialIsFollowed: { type: Boolean, default: false }
})

const emit = defineEmits(['change'])

const isFollowed = ref(props.initialIsFollowed)

const handleFollow = () => {
    // 模拟API调用
    // await followUser({ id: props.userId });
    isFollowed.value = !isFollowed.value
    emit('change', isFollowed.value)
    uni.$u.toast(isFollowed.value ? '关注成功' : '已取消关注')
}
</script>

<style scoped>
.follow-btn {
    display: inline-block;
}
</style>
