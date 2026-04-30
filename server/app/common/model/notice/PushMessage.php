<?php
namespace app\common\model\notice;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 推送消息记录模型
 * 开发者：杰哥网络科技 qq2711793818 杰哥
 */
class PushMessage extends BaseModel
{
    use SoftDelete;

    protected $name = 'push_message';

    protected $deleteTime = 'delete_time';

    public function user()
    {
        return $this->belongsTo(\app\common\model\user\User::class, 'user_id');
    }

    public function merchant()
    {
        return $this->belongsTo(\app\common\model\merchant\Merchant::class, 'merchant_id');
    }

    public function article()
    {
        return $this->belongsTo(\app\common\model\article\Article::class, 'article_id');
    }

    public static function getUnreadCount($userId)
    {
        return self::where([
            'user_id' => $userId,
            'is_read' => 0
        ])->count();
    }

    public static function hasPushed($userId, $articleId)
    {
        return self::where([
            'user_id' => $userId,
            'article_id' => $articleId
        ])->where('delete_time', null)->findOrEmpty()->isEmpty() ? false : true;
    }
}
