#!/bin/bash
# =========================================
# 开发者公众号：杰哥网络科技
# QQ: 2711793818 杰哥
# 聊天室WebSocket服务启动脚本
# 自动识别路径，上传即可使用
# =========================================

# 自动获取脚本所在目录
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
WORKDIR="$SCRIPT_DIR"

# 自动查找PHP
find_php() {
    local php_paths=(
        "/www/server/php/*/bin/php"
        "/usr/local/php/bin/php"
        "/usr/bin/php"
        "/usr/local/bin/php"
        "$(which php 2>/dev/null)"
    )

    for path in "${php_paths[@]}"; do
        if [ -x "$path" ]; then
            echo "$path"
            return 0
        fi
    done

    for path in /www/server/php/*/bin/php; do
        if [ -x "$path" ]; then
            echo "$path"
            return 0
        fi
    done

    return 1
}

PHP_BIN=$(find_php)
if [ -z "$PHP_BIN" ]; then
    echo "错误: 未找到PHP可执行文件"
    exit 1
fi

WEBSOCKET_PORT=8282

# 日志目录
LOG_DIR="$WORKDIR/runtime/websocket"
if [ ! -d "$LOG_DIR" ]; then
    mkdir -p "$LOG_DIR"
fi

LOG_FILE="$LOG_DIR/start.log"

cd $WORKDIR

check_process() {
    pgrep -f "workerman.*start.php" > /dev/null 2>&1
    return $?
}

start_service() {
    echo "========================================" >> $LOG_FILE
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] 正在启动聊天室服务..." >> $LOG_FILE
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] PHP路径: $PHP_BIN" >> $LOG_FILE
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] 工作目录: $WORKDIR" >> $LOG_FILE

    nohup $PHP_BIN $WORKDIR/app/websocket/start.php start >> $LOG_FILE 2>&1 &

    echo $! > $WORKDIR/runtime/websocket/chat.pid

    sleep 2
    if ps -p $! > /dev/null 2>&1; then
        echo "[$(date '+%Y-%m-%d %H:%M:%S')] 服务启动成功，PID: $!" >> $LOG_FILE
        echo "[$(date '+%Y-%m-%d %H:%M:%S')] WebSocket端口: $WEBSOCKET_PORT" >> $LOG_FILE
        echo "聊天室服务启动成功"
        return 0
    else
        echo "[$(date '+%Y-%m-%d %H:%M:%S')] 服务启动失败，请检查日志" >> $LOG_FILE
        echo "聊天室服务启动失败，请查看日志: $LOG_FILE"
        return 1
    fi
}

stop_service() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] 正在停止聊天室服务..." >> $LOG_FILE

    if [ -f $WORKDIR/runtime/websocket/chat.pid ]; then
        PID=$(cat $WORKDIR/runtime/websocket/chat.pid)
        if ps -p $PID > /dev/null 2>&1; then
            kill $PID
            sleep 1
            echo "[$(date '+%Y-%m-%d %H:%M:%S')] 服务已停止" >> $LOG_FILE
        else
            echo "[$(date '+%Y-%m-%d %H:%M:%S')] 服务未在运行" >> $LOG_FILE
        fi
        rm -f $WORKDIR/runtime/websocket/chat.pid
    else
        pkill -f "workerman.*start.php"
        echo "[$(date '+%Y-%m-%d %H:%M:%S')] 已尝试停止所有相关进程" >> $LOG_FILE
    fi
}

status_service() {
    if check_process; then
        echo "聊天室服务正在运行"
        echo "PHP路径: $PHP_BIN"
        echo "工作目录: $WORKDIR"
        if command -v ss > /dev/null 2>&1; then
            ss -tlnp | grep $WEBSOCKET_PORT
        elif command -v netstat > /dev/null 2>&1; then
            netstat -tlnp | grep $WEBSOCKET_PORT
        fi
    else
        echo "聊天室服务未运行"
    fi
}

case "$1" in
    start)
        if check_process; then
            echo "聊天室服务已在运行，请勿重复启动"
            status_service
        else
            start_service
        fi
        ;;
    stop)
        stop_service
        ;;
    restart)
        stop_service
        sleep 2
        start_service
        ;;
    status)
        status_service
        ;;
    *)
        echo "聊天室WebSocket服务管理脚本"
        echo "开发者公众号：杰哥网络科技"
        echo "QQ: 2711793818 杰哥"
        echo ""
        echo "用法: sh $0 {start|stop|restart|status}"
        echo ""
        echo "  start   - 启动服务"
        echo "  stop    - 停止服务"
        echo "  restart - 重启服务"
        echo "  status  - 查看状态"
        exit 1
        ;;
esac

exit 0
