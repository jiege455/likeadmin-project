<template>
    <u-popup :show="visible" mode="center" :round="10" @close="handleClose">
        <view class="slider-captcha">
            <view class="captcha-title">{{ title }}</view>
            
            <view class="captcha-container" v-if="captchaData.bg_img">
                <view class="bg-image-wrapper">
                    <image 
                        :src="captchaData.bg_img" 
                        class="bg-image" 
                        mode="aspectFit"
                        @load="onBgLoad"
                    />
                    <image
                        :src="captchaData.slider_img"
                        class="slider-image"
                        :style="sliderStyle"
                    />
                </view>
                
                <view class="slider-track" :class="{ success: isSuccess, fail: isFail }">
                    <view class="slider-bg" :style="{ width: sliderLeft + 'px' }"></view>
                    <view
                        class="slider-btn"
                        :style="{ left: sliderLeft + 'px' }"
                        @touchstart="handleTouchStart"
                        @touchmove="handleTouchMove"
                        @touchend="handleTouchEnd"
                        @mousedown="handleMouseDown"
                    >
                        <u-icon v-if="isSuccess" name="checkmark" color="#52c41a" size="20" />
                        <u-icon v-else-if="isFail" name="close" color="#ff4d4f" size="20" />
                        <u-icon v-else name="arrow-right" size="20" />
                    </view>
                </view>
                
                <view class="captcha-tips">{{ tips }}</view>
            </view>
            
            <view class="loading-wrapper" v-else>
                <u-loading-icon size="32" />
                <text>加载中...</text>
            </view>
            
            <view class="captcha-footer">
                <u-button @click="refreshCaptcha" :loading="loading" size="small">
                    <u-icon name="reload" size="14" />
                    <text class="ml-1">换一张</text>
                </u-button>
            </view>
        </view>
    </u-popup>
</template>

<script setup lang="ts">
/**
 * 滑块验证码组件 - UniApp
 * 开发者：杰哥网络科技
 * QQ: 2711793818
 */
import { ref, watch, computed } from 'vue'
import { captchaGet, captchaCheck } from '@/api/captcha'

interface CaptchaData {
    bg_img: string
    slider_img: string
    key: string
    y: number
    max_x?: number
}

const BG_WIDTH = 340
const BG_HEIGHT = 191
const SLIDER_WIDTH = 50
const SLIDER_HEIGHT = 50

const props = defineProps({
    show: {
        type: Boolean,
        default: false
    },
    title: {
        type: String,
        default: '安全验证'
    },
    tips: {
        type: String,
        default: '向右拖动滑块完成验证'
    }
})

const emit = defineEmits(['update:show', 'success', 'fail'])

const visible = computed({
    get: () => props.show,
    set: (val) => emit('update:show', val)
})

const loading = ref(false)
const captchaData = ref<CaptchaData>({
    bg_img: '',
    slider_img: '',
    key: '',
    y: 0,
    max_x: BG_WIDTH - SLIDER_WIDTH
})

const sliderLeft = ref(0)
const isSuccess = ref(false)
const isFail = ref(false)
const isDragging = ref(false)
const startX = ref(0)
const wrapperRect = ref<{ left: number; top: number }>({ left: 0, top: 0 })

watch(visible, (val) => {
    if (val) {
        getCaptcha()
    }
})

const sliderStyle = computed(() => {
    return {
        top: captchaData.value.y + 'px',
        left: sliderLeft.value + 'px'
    }
})

const onBgLoad = () => {
    uni.createSelectorQuery()
        .select('.bg-image-wrapper')
        .boundingClientRect((rect: any) => {
            if (rect) {
                wrapperRect.value = {
                    left: rect.left,
                    top: rect.top
                }
            }
        })
        .exec()
}

const getCaptcha = async () => {
    loading.value = true
    sliderLeft.value = 0
    isSuccess.value = false
    isFail.value = false
    try {
        const res = await captchaGet()
        captchaData.value = res
    } catch (error) {
        console.error('获取验证码失败', error)
        uni.$u.toast('获取验证码失败')
    } finally {
        loading.value = false
    }
}

const refreshCaptcha = () => {
    getCaptcha()
}

const handleTouchStart = (e: TouchEvent) => {
    if (isSuccess.value || isFail.value) return
    
    isDragging.value = true
    startX.value = e.touches[0].clientX - sliderLeft.value
}

const handleTouchMove = (e: TouchEvent) => {
    if (!isDragging.value) return
    
    let left = e.touches[0].clientX - startX.value
    const maxX = captchaData.value.max_x || (BG_WIDTH - SLIDER_WIDTH)
    
    if (left < 0) left = 0
    if (left > maxX) left = maxX
    
    sliderLeft.value = Math.round(left)
}

const handleTouchEnd = () => {
    if (!isDragging.value) return
    isDragging.value = false
    verifyCaptcha()
}

const handleMouseDown = (e: MouseEvent) => {
    if (isSuccess.value || isFail.value) return
    isDragging.value = true
    startX.value = e.clientX - sliderLeft.value
    
    const handleMouseMove = (moveE: MouseEvent) => {
        if (!isDragging.value) return
        
        let left = moveE.clientX - startX.value
        const maxX = captchaData.value.max_x || (BG_WIDTH - SLIDER_WIDTH)
        
        if (left < 0) left = 0
        if (left > maxX) left = maxX
        
        sliderLeft.value = Math.round(left)
    }
    
    const handleMouseUp = () => {
        if (!isDragging.value) return
        isDragging.value = false
        document.removeEventListener('mousemove', handleMouseMove)
        document.removeEventListener('mouseup', handleMouseUp)
        verifyCaptcha()
    }
    
    document.addEventListener('mousemove', handleMouseMove)
    document.addEventListener('mouseup', handleMouseUp)
}

const verifyCaptcha = async () => {
    if (sliderLeft.value < 10) {
        sliderLeft.value = 0
        return
    }
    
    try {
        const res = await captchaCheck({
            key: captchaData.value.key,
            x: sliderLeft.value
        })
        
        if (res.verified) {
            isSuccess.value = true
            setTimeout(() => {
                emit('success', captchaData.value.key)
                handleClose()
            }, 500)
        } else {
            isFail.value = true
            emit('fail')
            setTimeout(() => {
                refreshCaptcha()
            }, 1000)
        }
    } catch (error) {
        isFail.value = true
        uni.$u.toast('验证失败，请重试')
        setTimeout(() => {
            refreshCaptcha()
        }, 1000)
    }
}

const handleClose = () => {
    visible.value = false
    sliderLeft.value = 0
    isSuccess.value = false
    isFail.value = false
}

defineExpose({
    refresh: refreshCaptcha
})
</script>

<style lang="scss" scoped>
.slider-captcha {
    width: 380px;
    padding: 20px;
    user-select: none;
    
    .captcha-title {
        text-align: center;
        font-size: 16px;
        font-weight: bold;
        margin-bottom: 15px;
    }
    
    .captcha-container {
        position: relative;
    }
    
    .bg-image-wrapper {
        position: relative;
        width: 340px;
        height: 191px;
        margin: 0 auto;
        border-radius: 4px;
        overflow: hidden;
        background: #f5f5f5;
        
        .bg-image {
            width: 340px;
            height: 191px;
            display: block;
        }
        
        .slider-image {
            position: absolute;
            width: 50px;
            height: 50px;
        }
    }
    
    .slider-track {
        position: relative;
        width: 340px;
        height: 40px;
        margin: 15px auto 0;
        background: #f0f0f0;
        border-radius: 4px;
        border: 1px solid #e8e8e8;
        
        &.success {
            background: #d4edda;
            border-color: #c3e6cb;
            
            .slider-bg {
                background: #52c41a;
            }
        }
        
        &.fail {
            background: #f8d7da;
            border-color: #f5c6cb;
            
            .slider-bg {
                background: #ff4d4f;
            }
        }
        
        .slider-bg {
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            background: linear-gradient(90deg, #1991fa, #52c41a);
            border-radius: 4px 0 0 4px;
            transition: background 0.3s;
        }
        
        .slider-btn {
            position: absolute;
            top: 0;
            width: 40px;
            height: 40px;
            background: #fff;
            border-radius: 4px;
            box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
        }
    }
    
    .captcha-tips {
        text-align: center;
        color: #999;
        font-size: 12px;
        margin-top: 10px;
    }
    
    .loading-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 200px;
        color: #999;
        
        text {
            margin-top: 10px;
        }
    }
    
    .captcha-footer {
        display: flex;
        justify-content: center;
        margin-top: 15px;
    }
}
</style>
