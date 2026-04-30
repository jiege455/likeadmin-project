<?php
namespace app\api\controller\merchant;

use app\api\controller\BaseApiController;
use app\api\logic\merchant\ComplaintLogic;
use app\common\model\merchant\MerchantComplaint;

class ComplaintController extends BaseApiController
{
    public function add()
    {
        $params = $this->request->post();
        
        if (empty($params['content'])) {
            return $this->fail('请输入举报内容');
        }
        
        $type = $params['type'] ?? MerchantComplaint::TYPE_MERCHANT;
        $targetId = $params['target_id'] ?? 0;
        
        if ($type == MerchantComplaint::TYPE_MERCHANT) {
            if (empty($targetId) && empty($params['merchant_id'])) {
                return $this->fail('参数缺失: merchant_id');
            }
        } elseif ($type == MerchantComplaint::TYPE_ARTICLE) {
            if (empty($targetId)) {
                return $this->fail('参数缺失: target_id');
            }
        }
        
        $result = ComplaintLogic::add($params, $this->userId);
        if (false === $result) {
            return $this->fail(ComplaintLogic::getError());
        }
        return $this->success('提交成功');
    }
    
    public function reasons()
    {
        $reasons = [
            MerchantComplaint::TYPE_MERCHANT => [
                '虚假宣传',
                '服务态度差',
                '欺诈行为',
                '侵权内容',
                '其他原因'
            ],
            MerchantComplaint::TYPE_ARTICLE => [
                '内容虚假',
                '质量低劣',
                '侵权内容',
                '违法违规',
                '其他原因'
            ]
        ];
        return $this->success('获取成功', $reasons);
    }
}
