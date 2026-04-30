<?php

namespace app\common\service;

use Email;
use app\common\service\ConfigService;

class EmailService
{
    private static $error = '';

    public function send($to, $subject, $content): bool
    {
        $config = ConfigService::get('email', 'email_config', []);
        
        if (empty($config) || empty($config['smtp_server']) || empty($config['username'])) {
            self::$error = '邮箱服务未配置';
            return false;
        }

        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            self::$error = '邮箱格式不正确';
            return false;
        }

        try {
            $from = !empty($config['from_email']) ? $config['from_email'] : $config['username'];
            $encrypt = $config['encrypt'] ?? 'ssl';
            
            $email = new Email(
                $config['smtp_server'],
                $config['port'] ?? 465,
                $config['username'],
                $config['password'],
                $from,
                $encrypt
            );

            $result = $email->send($to, $subject, $content);
            
            if (!$result) {
                self::$error = $email->getError() ?: '邮件发送失败';
            }
            
            return $result;
        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

    public function sendToAdmin($subject, $content): bool
    {
        $config = ConfigService::get('email', 'email_config', []);
        if (empty($config) || empty($config['admin_email'])) {
            self::$error = '管理员邮箱未配置';
            return false;
        }
        return $this->send($config['admin_email'], $subject, $content);
    }

    public function getError(): string
    {
        return self::$error;
    }
}
