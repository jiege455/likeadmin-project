<?php

namespace app\common\model\notice;

use app\common\model\BaseModel;

class EmailLog extends BaseModel
{
    protected $name = 'email_log';

    protected $deleteTime = 'delete_time';

    public function getSendStatusDescAttr($value, $data)
    {
        $status = [
            0 => '发送中',
            1 => '成功',
            2 => '失败',
        ];
        return $status[$data['send_status']] ?? '';
    }

    public function getSendTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }
}
