<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 后台聊天设置控制器
 */
namespace app\adminapi\controller\chat;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\chat\ChatSettingLogic;

class ChatSettingController extends BaseAdminController
{
    public function getConfig()
    {
        $result = ChatSettingLogic::getConfig();
        return $this->data($result);
    }

    public function setConfig()
    {
        $params = $this->request->post();
        ChatSettingLogic::setConfig($params);
        return $this->success('保存成功');
    }
}
