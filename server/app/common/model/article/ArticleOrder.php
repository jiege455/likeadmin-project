<?php
namespace app\common\model\article;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

/**
 * 文章订单模型
 */
class ArticleOrder extends BaseModel
{
    use SoftDelete;

    protected $deleteTime = 'delete_time';
}
