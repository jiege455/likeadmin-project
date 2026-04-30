<?php
namespace app\adminapi\controller\article;

use app\adminapi\controller\BaseAdminController;
use app\adminapi\logic\article\ArticleAuditLogic;

class AuditController extends BaseAdminController
{
    public function lists()
    {
        return $this->data(ArticleAuditLogic::lists($this->request->get()));
    }

    public function detail()
    {
        $id = $this->request->get('id');
        return $this->data(ArticleAuditLogic::detail($id));
    }

    public function audit()
    {
        $params = $this->request->post();
        ArticleAuditLogic::audit($params);
        return $this->success('操作成功');
    }

    public function batchAudit()
    {
        $params = $this->request->post();
        ArticleAuditLogic::batchAudit($params);
        return $this->success('操作成功');
    }

    public function statistics()
    {
        return $this->data(ArticleAuditLogic::statistics());
    }
}
