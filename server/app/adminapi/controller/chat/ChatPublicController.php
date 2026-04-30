<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 公共聊天室消息控制器
 */
namespace app\adminapi\controller\chat;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\chat\ChatPublicLists;
use think\response\Json;

class ChatPublicController extends BaseAdminController
{
    public function lists(): Json
    {
        return $this->dataLists(new ChatPublicLists());
    }
}
