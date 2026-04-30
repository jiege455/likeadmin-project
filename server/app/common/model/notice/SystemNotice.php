<?php
namespace app\common\model\notice;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

class SystemNotice extends BaseModel
{
    use SoftDelete;
    protected $name = 'system_notice';
    protected $deleteTime = 'delete_time';

    const TYPE_NORMAL = 1;
    const TYPE_IMPORTANT = 2;
    const TYPE_ACTIVITY = 3;

    public static function getTypeDesc($type = null)
    {
        $desc = [
            self::TYPE_NORMAL => '普通',
            self::TYPE_IMPORTANT => '重要',
            self::TYPE_ACTIVITY => '活动',
        ];
        if ($type !== null) {
            return $desc[$type] ?? '未知';
        }
        return $desc;
    }

    public function getCreateTimeAttr($value)
    {
        return $value ? date('Y-m-d H:i:s', $value) : '';
    }

    public function getTypeTextAttr($value, $data)
    {
        return self::getTypeDesc($data['type'] ?? self::TYPE_NORMAL);
    }
}
