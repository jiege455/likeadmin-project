<?php
// +----------------------------------------------------------------------
// | likeadmin 快速开发前后端分离管理后台（PHP 版）
// +----------------------------------------------------------------------
// | 系统开关配置控制器
// +----------------------------------------------------------------------
// | author: 杰哥网络科技 QQ:2711793818
// +----------------------------------------------------------------------

namespace app\adminapi\controller\setting;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\setting\EmailLogic;
use think\response\Json;

/**
 * 系统开关配置控制器
 */
class SystemSwitchController extends BaseAdminController
{
    public array $notNeedLogin = ['config'];

    /**
     * 获取系统开关配置
     */
    public function config(): Json
    {
        $result = EmailLogic::getSwitchConfig();
        return $this->data($result);
    }

    /**
     * 保存系统开关配置
     */
    public function setConfig(): Json
    {
        $params = $this->request->post();
        $result = EmailLogic::setSwitchConfig($params);
        if (true === $result) {
            return $this->success('保存成功', []);
        }
        return $this->fail($result);
    }
}
