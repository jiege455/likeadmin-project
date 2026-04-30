<?php
/**
 * 敏感数据加密助手
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\common\utils;

class SensitiveData
{
    private static $key = '';

    private static function getKey()
    {
        if (empty(self::$key)) {
            self::$key = env('SENSITIVE_KEY', 'likeadmin_sensitive_key_2024');
        }
        return self::$key;
    }

    public static function encrypt($data)
    {
        if (empty($data)) {
            return $data;
        }

        $key = self::getKey();
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        
        return base64_encode($iv . $encrypted);
    }

    public static function decrypt($data)
    {
        if (empty($data)) {
            return $data;
        }

        $key = self::getKey();
        $data = base64_decode($data);
        
        if (strlen($data) < 16) {
            return $data;
        }

        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);

        $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        
        return $decrypted !== false ? $decrypted : $data;
    }

    public static function maskIdCard($idCard)
    {
        if (empty($idCard) || strlen($idCard) < 8) {
            return $idCard;
        }
        return substr($idCard, 0, 4) . '**********' . substr($idCard, -4);
    }

    public static function maskBankCard($cardNo)
    {
        if (empty($cardNo) || strlen($cardNo) < 8) {
            return $cardNo;
        }
        return substr($cardNo, 0, 4) . ' **** **** ' . substr($cardNo, -4);
    }

    public static function maskPhone($phone)
    {
        if (empty($phone) || strlen($phone) < 7) {
            return $phone;
        }
        return substr($phone, 0, 3) . '****' . substr($phone, -4);
    }

    public static function maskEmail($email)
    {
        if (empty($email) || strpos($email, '@') === false) {
            return $email;
        }
        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1];
        
        $nameLen = strlen($name);
        if ($nameLen <= 2) {
            $maskedName = $name[0] . '***';
        } else {
            $maskedName = substr($name, 0, 2) . '***' . substr($name, -1);
        }
        
        return $maskedName . '@' . $domain;
    }

    public static function isEncrypted($data)
    {
        if (empty($data)) {
            return false;
        }
        
        $decoded = base64_decode($data, true);
        if ($decoded === false) {
            return false;
        }
        
        return strlen($decoded) >= 16;
    }
}
