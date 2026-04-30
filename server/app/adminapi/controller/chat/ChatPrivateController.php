<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 私聊消息控制器
 */
namespace app\adminapi\controller\chat;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\chat\ChatPrivateLists;
use think\response\Json;

class ChatPrivateController extends BaseAdminController
{
    public function lists(): Json
    {
        return $this->dataLists(new ChatPrivateLists());
    }
}
