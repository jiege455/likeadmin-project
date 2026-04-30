<?php
/**
 * 文章水印配置控制器
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\adminapi\controller\setting;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\setting\ArticleWatermarkLogic;

class ArticleWatermarkController extends BaseAdminController
{
    public function getConfig()
    {
        $result = ArticleWatermarkLogic::getConfig();
        return $this->data($result);
    }

    public function setConfig()
    {
        $params = $this->request->post();
        ArticleWatermarkLogic::setConfig($params);
        return $this->success('操作成功');
    }
}