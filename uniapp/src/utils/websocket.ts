/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * WebSocket连接管理工具
 */

interface WebSocketOptions {
    url: string
    onOpen?: () => void
    onMessage?: (data: any) => void
    onClose?: () => void
    onError?: (error: any) => void
    reconnect?: boolean
    reconnectInterval?: number
    heartbeatInterval?: number
}

interface MessageData {
    type: string
    [key: string]: any
}

class WebSocketManager {
    private ws: UniApp.SocketTask | null = null
    private options: WebSocketOptions
    private isConnected = false
    private reconnectTimer: any = null
    private heartbeatTimer: any = null
    private reconnectAttempts = 0
    private maxReconnectAttempts = 5

    constructor(options: WebSocketOptions) {
        this.options = {
            reconnect: true,
            reconnectInterval: 3000,
            heartbeatInterval: 30000,
            ...options
        }
    }

    connect(): void {
        if (this.ws) {
            this.disconnect()
        }

        try {
            this.ws = uni.connectSocket({
                url: this.options.url,
                success: () => {
                    console.log('[WebSocket] 连接中...')
                },
                fail: (err) => {
                    console.error('[WebSocket] 连接失败:', err)
                    this.handleReconnect()
                }
            })

            this.bindEvents()
        } catch (error) {
            console.error('[WebSocket] 创建连接失败:', error)
            this.handleReconnect()
        }
    }

    private bindEvents(): void {
        if (!this.ws) return

        this.ws.onOpen(() => {
            console.log('[WebSocket] 连接成功')
            this.isConnected = true
            this.reconnectAttempts = 0
            this.startHeartbeat()
            this.options.onOpen?.()
        })

        this.ws.onMessage((res) => {
            try {
                const data = JSON.parse(res.data as string)

                if (data.type === 'ping') {
                    this.send({ type: 'pong' })
                    return
                }

                console.log('[WebSocket] 收到消息:', data)
                this.options.onMessage?.(data)
            } catch (error) {
                console.error('[WebSocket] 消息解析失败:', error)
            }
        })

        this.ws.onClose(() => {
            console.log('[WebSocket] 连接关闭')
            this.isConnected = false
            this.stopHeartbeat()
            this.options.onClose?.()
            this.handleReconnect()
        })

        this.ws.onError((err) => {
            console.error('[WebSocket] 连接错误:', err)
            this.isConnected = false
            this.options.onError?.(err)
        })
    }

    send(data: MessageData): boolean {
        if (!this.ws || !this.isConnected) {
            console.warn('[WebSocket] 未连接，无法发送消息')
            return false
        }

        try {
            this.ws.send({
                data: JSON.stringify(data),
                success: () => {
                    console.log('[WebSocket] 消息发送成功')
                },
                fail: (err) => {
                    console.error('[WebSocket] 消息发送失败:', err)
                }
            })
            return true
        } catch (error) {
            console.error('[WebSocket] 发送消息异常:', error)
            return false
        }
    }

    private startHeartbeat(): void {
        this.stopHeartbeat()

        this.heartbeatTimer = setInterval(() => {
            if (this.isConnected) {
                this.send({ type: 'pong' })
            }
        }, this.options.heartbeatInterval)
    }

    private stopHeartbeat(): void {
        if (this.heartbeatTimer) {
            clearInterval(this.heartbeatTimer)
            this.heartbeatTimer = null
        }
    }

    private handleReconnect(): void {
        if (!this.options.reconnect) return

        if (this.reconnectTimer) {
            clearTimeout(this.reconnectTimer)
        }

        if (this.reconnectAttempts >= this.maxReconnectAttempts) {
            console.log('[WebSocket] 达到最大重连次数，停止重连')
            return
        }

        this.reconnectAttempts++
        console.log(
            `[WebSocket] ${this.options.reconnectInterval / 1000}秒后尝试重连 (${
                this.reconnectAttempts
            }/${this.maxReconnectAttempts})`
        )

        this.reconnectTimer = setTimeout(() => {
            this.connect()
        }, this.options.reconnectInterval)
    }

    disconnect(): void {
        this.stopHeartbeat()

        if (this.reconnectTimer) {
            clearTimeout(this.reconnectTimer)
            this.reconnectTimer = null
        }

        if (this.ws) {
            this.ws.close({
                success: () => {
                    console.log('[WebSocket] 已断开连接')
                }
            })
            this.ws = null
        }

        this.isConnected = false
    }

    getConnectionStatus(): boolean {
        return this.isConnected
    }
}

export default WebSocketManager
