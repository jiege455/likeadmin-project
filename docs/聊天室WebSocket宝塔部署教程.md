# 聊天室 WebSocket 服务 - 宝塔面板部署教程

> 开发者公众号：杰哥网络科技
> QQ: 2711793818 杰哥

---

## 一、功能说明

聊天室功能使用 **GatewayWorker**（基于 Workerman）实现 WebSocket 实时通信，需要单独启动 WebSocket 服务才能正常使用。

### 服务架构

```
┌─────────────────────────────────────────────────────────────┐
│                    GatewayWorker 架构                        │
├─────────────────────────────────────────────────────────────┤
│  ┌──────────────┐    ┌──────────────┐    ┌──────────────┐  │
│  │   Gateway    │◄──►│   Register   │◄──►│BusinessWorker│  │
│  │  端口: 8282  │    │  端口: 1238  │    │  事件处理    │  │
│  │  客户端连接  │    │  服务注册    │    │  业务逻辑    │  │
│  └──────────────┘    └──────────────┘    └──────────────┘  │
└─────────────────────────────────────────────────────────────┘
```

### 需要开放的端口

| 端口 | 用途 | 说明 |
|------|------|------|
| 8282 | WebSocket | 客户端连接端口，必须对外开放 |
| 1238 | Register | 内部服务注册，仅需内网访问 |

---

## 二、宝塔面板配置

### 2.1 安装 PHP 扩展

1. 登录宝塔面板
2. 点击【软件商店】→ 找到已安装的 PHP 版本 → 点击【设置】
3. 选择【安装扩展】选项卡
4. 安装以下扩展：
   - **pcntl**（进程控制，必需）
   - **posix**（POSIX函数，必需）
   - **openssl**（已安装可忽略）

![安装扩展示意](安装pcntl和posix扩展)

### 2.2 删除禁用函数

1. 在 PHP 设置页面，选择【禁用函数】选项卡
2. 删除以下函数（如果存在）：
   - `putenv`
   - `pcntl_signal_dispatch`
   - `pcntl_wait`
   - `pcntl_signal`
   - `pcntl_alarm`
   - `pcntl_fork`
   - `shell_exec`
   - `exec`

![删除禁用函数示意](删除禁用函数)

### 2.3 开放防火墙端口

1. 点击【安全】菜单
2. 添加端口规则：
   - 端口：`8282`
   - 协议：TCP
   - 策略：允许
   - 备注：WebSocket 聊天服务

![开放端口示意](开放8282端口)

### 2.4 云服务器安全组

如果使用阿里云/腾讯云/华为云等，还需要在云控制台开放端口：

1. 登录云服务器控制台
2. 找到【安全组】设置
3. 添加入站规则：
   - 端口：`8282`
   - 协议：TCP
   - 来源：`0.0.0.0/0`

---

## 三、启动 WebSocket 服务

### 3.1 方式一：命令行启动（推荐用于调试）

```bash
# 进入项目目录
cd /www/wwwroot/你的网站目录/server

# 启动服务（调试模式，可看到日志输出）
php start.php start

# 看到以下输出表示成功
# ---------------------------------------------- WORKERMAN -----------------------------------------------
# Workerman version:4.x.x          PHP version:8.x.x
# ----------------------------------------------- WORKERS ------------------------------------------------
# worker              listen                    processes status
# ChatGateway         websocket://0.0.0.0:8282  4         [ok]
# ChatBusinessWorker  text://0.0.0.0:8283       4         [ok]
# ChatRegister        text://0.0.0.0:1238       1         [ok]
# ---------------------------------------------------------------------------------------------------------
# Press Ctrl+C to stop. Start success.
```

### 3.2 方式二：后台运行（推荐用于生产）

```bash
# 后台启动
php start.php start -d

# 停止服务
php start.php stop

# 重启服务
php start.php restart

# 查看状态
php start.php status
```

### 3.3 方式三：使用 Supervisor 守护进程（强烈推荐）

**Supervisor** 可以让 WebSocket 服务崩溃后自动重启，并且开机自启动。

#### 步骤 1：安装 Supervisor

在宝塔面板：
1. 点击【软件商店】
2. 搜索【Supervisor】
3. 点击【安装】

#### 步骤 2：添加守护进程

1. 安装完成后，点击【设置】
2. 点击【添加守护进程】
3. 填写配置：

| 配置项 | 值 |
|--------|-----|
| 名称 | `websocket-chat` |
| 运行目录 | `/www/wwwroot/你的网站目录/server/app/websocket` |
| 启动命令 | `php start.php start` |
| 进程数量 | `1` |
| 用户 | `www` |

4. 点击【确定】保存

#### 步骤 3：验证运行状态

在 Supervisor 管理页面，看到状态为 `RUNNING` 表示正常运行。

---

## 四、配置 WebSocket 地址

### 4.1 修改后端配置

编辑文件：`server/.env`

```env
# WebSocket 配置
WEBSOCKET_URL=ws://你的域名或IP:8282
```

或编辑文件：`server/config/project.php`

```php
return [
    // ... 其他配置
    
    // WebSocket配置 - 修改为你的实际地址
    'websocket_url' => 'ws://你的域名或IP:8282',
];
```

### 4.2 使用 WSS（SSL 加密连接）

如果网站使用 HTTPS，WebSocket 需要使用 WSS 协议：

#### 方法一：通过 Nginx 代理（推荐）

在宝塔面板，网站设置 → 配置文件，添加：

```nginx
location /ws {
    proxy_pass http://127.0.0.1:8282;
    proxy_http_version 1.1;
    proxy_set_header Upgrade $http_upgrade;
    proxy_set_header Connection "upgrade";
    proxy_set_header Host $host;
    proxy_set_header X-Real-IP $remote_addr;
    proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    proxy_read_timeout 86400;
}
```

然后配置地址改为：
```php
'websocket_url' => 'wss://你的域名/ws',
```

#### 方法二：直接配置 SSL

编辑 `start_gateway.php`：

```php
// 修改为
$gateway = new Gateway("websocket://0.0.0.0:8282");
$gateway->transport = 'ssl';

// 添加 SSL 证书
$gateway->context = [
    'ssl' => [
        'local_cert'  => '/www/server/panel/vhost/cert/你的域名/fullchain.pem',
        'local_pk'    => '/www/server/panel/vhost/cert/你的域名/privkey.pem',
        'verify_peer' => false,
    ]
];
```

---

## 五、常见问题排查

### 5.1 服务启动失败

**错误：`pcntl_fork has been disabled for security reasons`**

解决：删除 PHP 禁用函数中的 `pcntl_*` 相关函数

**错误：`Address already in use`**

解决：端口被占用，检查是否有其他进程占用 8282 端口
```bash
# 查看端口占用
netstat -tlnp | grep 8282

# 杀掉占用进程
kill -9 进程ID
```

### 5.2 客户端连接失败

**检查清单：**

1. ✅ WebSocket 服务是否正在运行
   ```bash
   php start.php status
   ```

2. ✅ 防火墙是否开放 8282 端口

3. ✅ 云服务器安全组是否开放端口

4. ✅ 前端配置的 WebSocket 地址是否正确

5. ✅ 如果使用 HTTPS，必须使用 WSS 协议

### 5.3 服务自动停止

**原因：** 可能是 PHP 内存不足或超时

**解决：** 使用 Supervisor 守护进程，自动重启

### 5.4 消息发送失败

**检查：**

1. 数据库连接是否正常
2. 查看服务端日志输出
3. 检查用户 Token 是否有效

---

## 六、测试验证

### 6.1 测试 WebSocket 连接

在浏览器控制台执行：

```javascript
// 创建 WebSocket 连接
const ws = new WebSocket('ws://你的IP:8282');

// 连接成功
ws.onopen = function() {
    console.log('WebSocket 连接成功');
};

// 接收消息
ws.onmessage = function(e) {
    console.log('收到消息:', e.data);
};

// 连接失败
ws.onerror = function(e) {
    console.log('连接失败:', e);
};
```

### 6.2 测试聊天功能

1. 打开前端聊天室页面
2. 发送一条消息
3. 检查是否收到消息回显
4. 查看服务端日志是否有输出

---

## 七、文件路径参考

| 文件 | 路径 | 说明 |
|------|------|------|
| 主启动文件 | `server/app/websocket/start.php` | Linux 完整版启动 |
| Windows 启动 | `server/start_windows.php` | Windows 调试用 |
| Gateway 服务 | `server/app/websocket/start_gateway.php` | 客户端连接处理 |
| BusinessWorker | `server/app/websocket/start_businessworker.php` | 业务逻辑处理 |
| Register 服务 | `server/app/websocket/start_register.php` | 服务注册中心 |
| 事件处理 | `server/app/websocket/Events.php` | 消息事件处理 |
| 项目配置 | `server/config/project.php` | WebSocket URL 配置 |

---

## 八、快速启动命令汇总

```bash
# 进入目录
cd /www/wwwroot/你的网站目录/server/app/websocket

# 启动（调试模式）
php start.php start

# 启动（后台模式）
php start.php start -d

# 停止
php start.php stop

# 重启
php start.php restart

# 查看状态
php start.php status

# 查看连接数
php start.php connections
```

---

## 九、Windows 本地开发

如果是 Windows 本地开发环境（phpStudy），使用以下命令：

```powershell
# 进入 server 目录
cd E:\phpstudy_pro\WWW\hyl\likeadmin_php-master\server

# 启动 Windows 版本（单进程模式）
php start_windows.php
```

**注意：** Windows 版本不支持后台运行，需要保持命令行窗口打开。

---

> 如有问题，请联系开发者：杰哥网络科技 (QQ: 2711793818)
