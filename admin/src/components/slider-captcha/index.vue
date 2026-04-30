<template>
    <el-dialog
        v-model="visible"
        :title="title"
        width="400px"
        :close-on-click-modal="false"
        :close-on-press-escape="false"
        @close="handleClose"
    >
        <div class="slider-captcha">
            <div class="captcha-container" v-if="captchaData.bg_img">
                <div class="bg-image-wrapper" ref="bgWrapperRef">
                    <img :src="captchaData.bg_img" class="bg-image" @load="onBgLoad" />
                    <img
                        :src="captchaData.slider_img"
                        class="slider-image"
                        :style="sliderStyle"
                    />
                </div>
                <div class="slider-track" :class="{ success: isSuccess, fail: isFail }">
                    <div class="slider-bg" :style="{ width: sliderLeft + 'px' }"></div>
                    <div
                        class="slider-btn"
                        :style="{ left: sliderLeft + 'px' }"
                        @mousedown="handleMouseDown"
                        @touchstart="handleTouchStart"
                    >
                        <icon v-if="isSuccess" name="el-icon-Check" :size="20" color="#52c41a" />
                        <icon v-else-if="isFail" name="el-icon-Close" :size="20" color="#ff4d4f" />
                        <icon v-else name="el-icon-Right" :size="20" />
                    </div>
                </div>
                <div class="captcha-tips">{{ tips }}</div>
            </div>
            <div class="loading-wrapper" v-else>
                <el-icon class="is-loading"><Loading /></el-icon>
                <span>加载中...</span>
            </div>
        </div>
        <template #footer>
            <div class="captcha-footer">
                <el-button @click="refreshCaptcha" :loading="loading">
                    <icon name="el-icon-Refresh" :size="14" />
                    换一张
                </el-button>
            </div>
        </template>
    </el-dialog>
</template>

<script lang="ts" setup>
/**
 * 滑块验证码组件
 * 开发者：杰哥网络科技
 * QQ: 2711793818
 */
import { ref, watch, computed } from 'vue'
import { captchaGet, captchaCheck } from '@/api/captcha'
import { Loading } from '@element-plus/icons-vue'

interface CaptchaData {
    bg_img: string
    slider_img: string
    key: string
    y: number
    max_x?: number
}

const props = defineProps({
    modelValue: {
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

const emit = defineEmits(['update:modelValue', 'success', 'fail'])

const visible = computed({
    get: () => props.modelValue,
    set: (val) => emit('update:modelValue', val)
})

const loading = ref(false)
const captchaData = ref<CaptchaData>({
    bg_img: '',
    slider_img: '',
    key: '',
    y: 0,
    max_x: 290
})

const sliderLeft = ref(0)
const isSuccess = ref(false)
const isFail = ref(false)
const isDragging = ref(false)
const startX = ref(0)
const bgWrapperRef = ref<HTMLElement>()

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
    } finally {
        loading.value = false
    }
}

const refreshCaptcha = () => {
    getCaptcha()
}

const handleMouseDown = (e: MouseEvent) => {
    if (isSuccess.value || isFail.value) return
    e.preventDefault()
    
    isDragging.value = true
    startX.value = e.clientX - sliderLeft.value
    
    document.addEventListener('mousemove', handleMouseMove)
    document.addEventListener('mouseup', handleMouseUp)
}

const handleMouseMove = (e: MouseEvent) => {
    if (!isDragging.value) return
    
    let left = e.clientX - startX.value
    const maxX = captchaData.value.max_x || 290
    
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

const handleTouchStart = (e: TouchEvent) => {
    if (isSuccess.value || isFail.value) return
    e.preventDefault()
    
    isDragging.value = true
    startX.value = e.touches[0].clientX - sliderLeft.value
    
    document.addEventListener('touchmove', handleTouchMove, { passive: false })
    document.addEventListener('touchend', handleTouchEnd)
}

const handleTouchMove = (e: TouchEvent) => {
    if (!isDragging.value) return
    e.preventDefault()
    
    let left = e.touches[0].clientX - startX.value
    const maxX = captchaData.value.max_x || 290
    
    if (left < 0) left = 0
    if (left > maxX) left = maxX
    
    sliderLeft.value = Math.round(left)
}

const handleTouchEnd = () => {
    if (!isDragging.value) return
    isDragging.value = false
    
    document.removeEventListener('touchmove', handleTouchMove)
    document.removeEventListener('touchend', handleTouchEnd)
    
    verifyCaptcha()
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
    user-select: none;
    
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
            width: 100%;
            height: 100%;
            display: block;
        }
        
        .slider-image {
            position: absolute;
            width: 50px;
            height: 50px;
            cursor: move;
            filter: drop-shadow(2px 2px 4px rgba(0, 0, 0, 0.3));
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
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.1s;
            
            &:hover {
                transform: scale(1.05);
            }
            
            &:active {
                transform: scale(0.95);
            }
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
        height: 250px;
        color: #999;
        
        .el-icon {
            font-size: 32px;
            margin-bottom: 10px;
        }
    }
}

.captcha-footer {
    display: flex;
    justify-content: center;
}
</style>
