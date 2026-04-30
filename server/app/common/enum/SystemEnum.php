<?php

namespace app\common\enum;

class SystemEnum
{
    const VERIFY_TYPE_MOBILE = 'mobile';
    const VERIFY_TYPE_EMAIL = 'email';

    public static function getVerifyTypeDesc($type, $flag = false)
    {
        $desc = [
            self::VERIFY_TYPE_MOBILE => '手机验证',
            self::VERIFY_TYPE_EMAIL => '邮箱验证',
        ];

        if ($flag) {
            return $desc;
        }

        return $desc[$type] ?? '';
    }

    public static function getVerifyTypeOptions()
    {
        return [
            ['value' => self::VERIFY_TYPE_EMAIL, 'label' => '邮箱验证'],
            ['value' => self::VERIFY_TYPE_MOBILE, 'label' => '手机验证'],
        ];
    }
}
