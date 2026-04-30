<template>
    <view class="u-qrcode">
        <!-- #ifndef MP-WEIXIN || MP-QQ -->
        <canvas
            :id="canvasId"
            :canvas-id="canvasId"
            :style="{ width: size + 'px', height: size + 'px' }"
        ></canvas>
        <!-- #endif -->
        <!-- #ifdef MP-WEIXIN || MP-QQ -->
        <canvas
            type="2d"
            :id="canvasId"
            :canvas-id="canvasId"
            :style="{ width: size + 'px', height: size + 'px' }"
        ></canvas>
        <!-- #endif -->
    </view>
</template>

<script>
import UQRCode from './uqrcode.js'

export default {
    name: 'u-qrcode',
    props: {
        canvasId: {
            type: String,
            default: 'u-qrcode'
        },
        value: {
            type: String,
            default: ''
        },
        size: {
            type: Number,
            default: 200
        },
        options: {
            type: Object,
            default: () => ({})
        }
    },
    watch: {
        value: {
            handler(val) {
                if (val) {
                    this.make()
                }
            },
            immediate: true
        }
    },
    methods: {
        make() {
            this.$nextTick(() => {
                const ctx = uni.createCanvasContext(this.canvasId, this)
                const uqrcode = new UQRCode()
                uqrcode.data = this.value
                uqrcode.size = this.size
                uqrcode.make()
                uqrcode.draw(ctx)
            })
        }
    }
}
</script>

<style scoped>
.u-qrcode {
    display: inline-block;
}
</style>
