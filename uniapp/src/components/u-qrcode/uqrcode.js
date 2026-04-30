// uqrcode.js
// 这是一个简化版的二维码生成逻辑，实际项目中建议下载完整的 uqrcode.js 库
// 这里为了演示，我们假设它是一个可用的类
// 请务必替换为真实的 uqrcode.js 内容，或者使用 npm install uqrcodejs

class UQRCode {
    constructor() {
        this.data = ''
        this.size = 200
        this.modules = []
        this.moduleCount = 0
    }

    make() {
        // 简化的生成逻辑，实际需要引入 qrcode 算法
        // 这里我们直接模拟生成一个矩阵
        this.moduleCount = 33 // Version 4
        this.modules = Array(this.moduleCount)
            .fill(0)
            .map(() => Array(this.moduleCount).fill(false))

        // 填充定位点 (Finder Patterns)
        this.fillFinderPattern(0, 0)
        this.fillFinderPattern(this.moduleCount - 7, 0)
        this.fillFinderPattern(0, this.moduleCount - 7)

        // 随机填充一些点来模拟二维码 (仅用于演示占位)
        for (let r = 0; r < this.moduleCount; r++) {
            for (let c = 0; c < this.moduleCount; c++) {
                if (Math.random() > 0.5) {
                    this.modules[r][c] = true
                }
            }
        }
    }

    fillFinderPattern(row, col) {
        for (let r = -1; r <= 7; r++) {
            for (let c = -1; c <= 7; c++) {
                if (
                    row + r >= 0 &&
                    row + r < this.moduleCount &&
                    col + c >= 0 &&
                    col + c < this.moduleCount
                ) {
                    if (
                        r >= 0 &&
                        r <= 6 &&
                        c >= 0 &&
                        c <= 6 &&
                        !(r >= 1 && r <= 5 && c >= 1 && c <= 5)
                    ) {
                        this.modules[row + r][col + c] = true
                    }
                }
            }
        }
    }

    draw(ctx) {
        const tileW = this.size / this.moduleCount
        const tileH = this.size / this.moduleCount

        ctx.setFillStyle('black')
        for (let r = 0; r < this.moduleCount; r++) {
            for (let c = 0; c < this.moduleCount; c++) {
                if (this.modules[r][c]) {
                    ctx.fillRect(c * tileW, r * tileH, tileW, tileH)
                }
            }
        }
        ctx.draw()
    }
}

export default UQRCode
