<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * WebSocket事件处理类
 */
namespace app\websocket;

use GatewayWorker\Lib\Gateway;
use Workerman\MySQL\Connection;

class Events
{
    protected static $userConnections = [];
    protected static $db = null;
    protected static $config = [];

    public static function onWorkerStart($worker)
    {
        $envFile = ROOT_PATH . '.env';
        if (file_exists($envFile)) {
            $content = file_get_contents($envFile);
            $lines = explode("\n", $content);
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || strpos($line, '#') === 0) continue;
                if (strpos($line, '=') !== false) {
                    $parts = explode('=', $line, 2);
                    $key = trim($parts[0]);
                    $value = isset($parts[1]) ? trim($parts[1], " \t\n\r\0\x0B\"'") : '';
                    self::$config[$key] = $value;
                }
            }
        }
        
        echo "ChatBusinessWorker启动成功\n";
        echo "数据库配置: " . (self::$config['DATABASE'] ?? '未配置') . "\n";
    }

    protected static function getDb()
    {
        if (self::$db === null) {
            self::connectDb();
        }
        
        try {
            self::$db->query("SELECT 1");
        } catch (\Exception $e) {
            echo "数据库连接已断开，正在重连...\n";
            self::connectDb();
        }
        
        return self::$db;
    }
    
    protected static function connectDb()
    {
        try {
            $host = self::$config['HOSTNAME'] ?? '127.0.0.1';
            $port = self::$config['HOSTPORT'] ?? '3306';
            $database = self::$config['DATABASE'] ?? '';
            $username = self::$config['USERNAME'] ?? 'root';
            $password = self::$config['PASSWORD'] ?? '';
            $charset = self::$config['CHARSET'] ?? 'utf8mb4';
            
            $dsn = "mysql:host={$host};port={$port};dbname={$database};charset={$charset}";
            self::$db = new \PDO($dsn, $username, $password, [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::MYSQL_ATTR_INIT_COMMAND => "SET SESSION wait_timeout=28800, interactive_timeout=28800",
            ]);
            echo "数据库连接成功\n";
        } catch (\Exception $e) {
            echo "数据库连接失败: " . $e->getMessage() . "\n";
            self::$db = null;
        }
    }

    public static function onConnect($client_id)
    {
        Gateway::sendToClient($client_id, json_encode([
            'type' => 'init',
            'client_id' => $client_id,
            'msg' => '连接成功'
        ]));
    }

    public static function onMessage($client_id, $message)
    {
        $data = json_decode($message, true);
        
        if (!$data || !isset($data['type'])) {
            return;
        }

        switch ($data['type']) {
            case 'login':
                self::handleLogin($client_id, $data);
                break;
            case 'login_private':
                self::handlePrivateLogin($client_id, $data);
                break;
            case 'send':
                self::handleSend($client_id, $data);
                break;
            case 'send_private':
                self::handlePrivateSend($client_id, $data);
                break;
            case 'pong':
                break;
            default:
                Gateway::sendToClient($client_id, json_encode([
                    'type' => 'error',
                    'msg' => '未知的消息类型'
                ]));
        }
    }

    public static function onClose($client_id)
    {
        if (isset(self::$userConnections[$client_id])) {
            $userInfo = self::$userConnections[$client_id];
            unset(self::$userConnections[$client_id]);
            
            $roomId = $userInfo['room_id'] ?? 'public';
            
            if ($roomId === 'public') {
                $onlineCount = Gateway::getAllClientCount();
                Gateway::sendToAll(json_encode([
                    'type' => 'online',
                    'count' => $onlineCount,
                    'msg' => $userInfo['nickname'] . ' 离开了聊天室'
                ]));
            }
            
            echo "用户 {$userInfo['nickname']} 断开连接\n";
        }
    }

    protected static function checkBan($db, $prefix, $userId, $userType, $banType)
    {
        $stmt = $db->prepare("SELECT * FROM {$prefix}chat_ban WHERE user_id = ? AND user_type = ? AND status = 1 AND (ban_type = ? OR ban_type = 3)");
        $stmt->execute([$userId, $userType, $banType]);
        $ban = $stmt->fetch();
        
        if (!$ban) {
            return null;
        }
        
        if ($ban['expire_time'] && $ban['expire_time'] < time()) {
            $stmt = $db->prepare("UPDATE {$prefix}chat_ban SET status = 0, update_time = ? WHERE id = ?");
            $stmt->execute([time(), $ban['id']]);
            return null;
        }
        
        return $ban;
    }

    protected static function handleLogin($client_id, $data)
    {
        $token = $data['token'] ?? '';
        $roomId = $data['room_id'] ?? 'public';
        
        try {
            $db = self::getDb();
            if (!$db) {
                Gateway::sendToClient($client_id, json_encode([
                    'type' => 'error',
                    'msg' => '数据库连接失败'
                ]));
                return;
            }
            
            $prefix = self::$config['PREFIX'] ?? 'la_';
            
            $stmt = $db->prepare("SELECT user_id FROM {$prefix}user_session WHERE token = ? AND expire_time > ?");
            $stmt->execute([$token, time()]);
            $userSession = $stmt->fetch();
            
            if (!$userSession) {
                Gateway::sendToClient($client_id, json_encode([
                    'type' => 'error',
                    'msg' => '登录已过期，请重新登录'
                ]));
                return;
            }
            
            $stmt = $db->prepare("SELECT id, nickname, avatar FROM {$prefix}user WHERE id = ?");
            $stmt->execute([$userSession['user_id']]);
            $user = $stmt->fetch();
            
            if (!$user) {
                Gateway::sendToClient($client_id, json_encode([
                    'type' => 'error',
                    'msg' => '用户不存在'
                ]));
                return;
            }
            
            $banInfo = self::checkBan($db, $prefix, $user['id'], 1, 2);
            if ($banInfo) {
                Gateway::sendToClient($client_id, json_encode([
                    'type' => 'error',
                    'msg' => '您已被禁言，原因：' . $banInfo['reason']
                ]));
                return;
            }
            
            $avatar = $user['avatar'] ?: '/static/images/user/default_avatar.png';
            
            self::$userConnections[$client_id] = [
                'user_id' => $user['id'],
                'nickname' => $user['nickname'] ?: '游客' . $user['id'],
                'avatar' => $avatar,
                'room_id' => $roomId,
                'is_private' => false
            ];
            
            Gateway::joinGroup($client_id, $roomId);
            
            $onlineCount = Gateway::getAllClientCount();
            
            Gateway::sendToClient($client_id, json_encode([
                'type' => 'login_success',
                'user_id' => $user['id'],
                'nickname' => $user['nickname'],
                'avatar' => $avatar,
                'online_count' => $onlineCount,
                'room_id' => $roomId
            ]));
            
            Gateway::sendToGroup($roomId, json_encode([
                'type' => 'online',
                'count' => $onlineCount,
                'msg' => $user['nickname'] . ' 进入了聊天室'
            ]));
            
            echo "用户 {$user['nickname']} 登录成功，房间: {$roomId}\n";
            
        } catch (\Exception $e) {
            Gateway::sendToClient($client_id, json_encode([
                'type' => 'error',
                'msg' => '登录失败：' . $e->getMessage()
            ]));
            echo "登录错误: " . $e->getMessage() . "\n";
        }
    }

    protected static function handlePrivateLogin($client_id, $data)
    {
        $token = $data['token'] ?? '';
        $conversationId = $data['conversation_id'] ?? '';
        
        if (empty($conversationId)) {
            Gateway::sendToClient($client_id, json_encode([
                'type' => 'error',
                'msg' => '会话ID不能为空'
            ]));
            return;
        }
        
        try {
            $db = self::getDb();
            if (!$db) {
                Gateway::sendToClient($client_id, json_encode([
                    'type' => 'error',
                    'msg' => '数据库连接失败'
                ]));
                return;
            }
            
            $prefix = self::$config['PREFIX'] ?? 'la_';
            
            $stmt = $db->prepare("SELECT user_id FROM {$prefix}user_session WHERE token = ? AND expire_time > ?");
            $stmt->execute([$token, time()]);
            $userSession = $stmt->fetch();
            
            if (!$userSession) {
                Gateway::sendToClient($client_id, json_encode([
                    'type' => 'error',
                    'msg' => '登录已过期，请重新登录'
                ]));
                return;
            }
            
            $stmt = $db->prepare("SELECT id, nickname, avatar FROM {$prefix}user WHERE id = ?");
            $stmt->execute([$userSession['user_id']]);
            $user = $stmt->fetch();
            
            if (!$user) {
                Gateway::sendToClient($client_id, json_encode([
                    'type' => 'error',
                    'msg' => '用户不存在'
                ]));
                return;
            }
            
            $banInfo = self::checkBan($db, $prefix, $user['id'], 1, 1);
            if ($banInfo) {
                Gateway::sendToClient($client_id, json_encode([
                    'type' => 'error',
                    'msg' => '您已被禁止私聊，原因：' . $banInfo['reason']
                ]));
                return;
            }
            
            $avatar = $user['avatar'] ?: '/static/images/user/default_avatar.png';
            
            self::$userConnections[$client_id] = [
                'user_id' => $user['id'],
                'nickname' => $user['nickname'] ?: '游客' . $user['id'],
                'avatar' => $avatar,
                'room_id' => $conversationId,
                'is_private' => true
            ];
            
            Gateway::joinGroup($client_id, $conversationId);
            
            Gateway::sendToClient($client_id, json_encode([
                'type' => 'login_private_success',
                'user_id' => $user['id'],
                'nickname' => $user['nickname'],
                'avatar' => $avatar,
                'conversation_id' => $conversationId
            ]));
            
            echo "用户 {$user['nickname']} 登录私聊成功，会话: {$conversationId}\n";
            
        } catch (\Exception $e) {
            Gateway::sendToClient($client_id, json_encode([
                'type' => 'error',
                'msg' => '登录失败：' . $e->getMessage()
            ]));
            echo "私聊登录错误: " . $e->getMessage() . "\n";
        }
    }

    protected static function handleSend($client_id, $data)
    {
        if (!isset(self::$userConnections[$client_id])) {
            Gateway::sendToClient($client_id, json_encode([
                'type' => 'error',
                'msg' => '请先登录'
            ]));
            return;
        }
        
        $content = trim($data['content'] ?? '');
        $msgType = $data['msg_type'] ?? 1;
        
        if (empty($content)) {
            Gateway::sendToClient($client_id, json_encode([
                'type' => 'error',
                'msg' => '消息内容不能为空'
            ]));
            return;
        }
        
        if (mb_strlen($content) > 500) {
            Gateway::sendToClient($client_id, json_encode([
                'type' => 'error',
                'msg' => '消息内容不能超过500字'
            ]));
            return;
        }
        
        $userInfo = self::$userConnections[$client_id];
        $roomId = $userInfo['room_id'];
        
        try {
            $db = self::getDb();
            if (!$db) {
                Gateway::sendToClient($client_id, json_encode([
                    'type' => 'error',
                    'msg' => '数据库连接失败'
                ]));
                return;
            }
            
            $prefix = self::$config['PREFIX'] ?? 'la_';
            $createTime = time();
            
            $stmt = $db->prepare("INSERT INTO {$prefix}chat_message (room_id, user_id, nickname, avatar, content, msg_type, create_time) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $roomId,
                $userInfo['user_id'],
                $userInfo['nickname'],
                $userInfo['avatar'],
                $content,
                $msgType,
                $createTime
            ]);
            
            $messageId = $db->lastInsertId();
            
            $message = [
                'type' => 'message',
                'id' => $messageId,
                'user_id' => $userInfo['user_id'],
                'nickname' => $userInfo['nickname'],
                'avatar' => $userInfo['avatar'],
                'content' => $content,
                'msg_type' => $msgType,
                'create_time' => date('Y-m-d H:i:s', $createTime),
                'room_id' => $roomId
            ];
            
            Gateway::sendToGroup($roomId, json_encode($message));
            
            echo "消息 [{$roomId}] {$userInfo['nickname']}: {$content}\n";
            
        } catch (\Exception $e) {
            Gateway::sendToClient($client_id, json_encode([
                'type' => 'error',
                'msg' => '发送失败：' . $e->getMessage()
            ]));
            echo "发送消息错误: " . $e->getMessage() . "\n";
        }
    }

    protected static function handlePrivateSend($client_id, $data)
    {
        if (!isset(self::$userConnections[$client_id])) {
            Gateway::sendToClient($client_id, json_encode([
                'type' => 'error',
                'msg' => '请先登录'
            ]));
            return;
        }
        
        $content = trim($data['content'] ?? '');
        $msgType = $data['msg_type'] ?? 1;
        $conversationId = $data['conversation_id'] ?? '';
        
        if (empty($conversationId)) {
            Gateway::sendToClient($client_id, json_encode([
                'type' => 'error',
                'msg' => '会话ID不能为空'
            ]));
            return;
        }
        
        if (empty($content)) {
            Gateway::sendToClient($client_id, json_encode([
                'type' => 'error',
                'msg' => '消息内容不能为空'
            ]));
            return;
        }
        
        if (mb_strlen($content) > 500) {
            Gateway::sendToClient($client_id, json_encode([
                'type' => 'error',
                'msg' => '消息内容不能超过500字'
            ]));
            return;
        }
        
        $userInfo = self::$userConnections[$client_id];
        
        try {
            $db = self::getDb();
            if (!$db) {
                Gateway::sendToClient($client_id, json_encode([
                    'type' => 'error',
                    'msg' => '数据库连接失败'
                ]));
                return;
            }
            
            $prefix = self::$config['PREFIX'] ?? 'la_';
            $createTime = time();
            
            $stmt = $db->prepare("INSERT INTO {$prefix}chat_message (room_id, user_id, nickname, avatar, content, msg_type, create_time) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $conversationId,
                $userInfo['user_id'],
                $userInfo['nickname'],
                $userInfo['avatar'],
                $content,
                $msgType,
                $createTime
            ]);
            
            $messageId = $db->lastInsertId();
            
            $parts = explode('_', $conversationId);
            $targetUserId = null;
            if (count($parts) >= 3) {
                $id1 = (int)$parts[1];
                $id2 = (int)$parts[2];
                $targetUserId = ($id1 == $userInfo['user_id']) ? $id2 : $id1;
            }
            
            if ($targetUserId) {
                $stmt = $db->prepare("UPDATE {$prefix}chat_conversation SET last_message = ?, last_message_time = ?, update_time = ? WHERE conversation_id = ? AND user_id = ?");
                $stmt->execute([$content, $createTime, $createTime, $conversationId, $targetUserId]);
                $stmt->execute([$content, $createTime, $createTime, $conversationId, $userInfo['user_id']]);
                
                $stmt = $db->prepare("UPDATE {$prefix}chat_conversation SET unread_count = unread_count + 1 WHERE conversation_id = ? AND user_id = ?");
                $stmt->execute([$conversationId, $targetUserId]);
            }
            
            $message = [
                'type' => 'private_message',
                'id' => $messageId,
                'user_id' => $userInfo['user_id'],
                'nickname' => $userInfo['nickname'],
                'avatar' => $userInfo['avatar'],
                'content' => $content,
                'msg_type' => $msgType,
                'create_time' => date('Y-m-d H:i:s', $createTime),
                'conversation_id' => $conversationId
            ];
            
            Gateway::sendToGroup($conversationId, json_encode($message));
            
            echo "私聊消息 [{$conversationId}] {$userInfo['nickname']}: {$content}\n";
            
        } catch (\Exception $e) {
            Gateway::sendToClient($client_id, json_encode([
                'type' => 'error',
                'msg' => '发送失败：' . $e->getMessage()
            ]));
            echo "私聊消息错误: " . $e->getMessage() . "\n";
        }
    }
}
