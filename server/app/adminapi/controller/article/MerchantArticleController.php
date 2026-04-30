<?php
namespace app\adminapi\controller\article;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\lists\article\MerchantArticleLists;
use app\adminapi\logic\article\MerchantArticleLogic;

/**
 * 商户文章控制器
 */
class MerchantArticleController extends BaseAdminController
{
    /**
     * @notes 文章列表
     */
    public function lists()
    {
        return $this->dataLists(new MerchantArticleLists());
    }

    /**
     * @notes 文章详情
     */
    public function detail()
    {
        $params = $this->request->get();
        $result = MerchantArticleLogic::detail($params);
        return $this->data($result);
    }

    /**
     * @notes 审核文章
     */
    public function audit()
    {
        $params = $this->request->post();
        $result = MerchantArticleLogic::audit($params);
        if ($result) {
            return $this->success('操作成功');
        }
        return $this->fail(MerchantArticleLogic::getError());
    }

    /**
     * @notes 删除文章
     */
    public function delete()
    {
        $params = $this->request->post();
        MerchantArticleLogic::delete($params);
        return $this->success('删除成功');
    }
}
