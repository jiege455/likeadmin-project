<?php
namespace app\api\logic\merchant;

use app\common\logic\BaseLogic;
use app\common\model\merchant\MerchantComplaint;

class ComplaintLogic extends BaseLogic
{
    public static function add($params, $userId)
    {
        try {
            $type = $params['type'] ?? MerchantComplaint::TYPE_MERCHANT;
            $targetId = $params['target_id'] ?? 0;
            
            $data = [
                'user_id' => $userId,
                'type' => $type,
                'target_id' => $targetId,
                'content' => $params['content'],
                'images' => isset($params['images']) ? json_encode($params['images']) : '',
                'contact' => $params['contact'] ?? '',
                'reason' => $params['reason'] ?? '',
                'status' => 0,
            ];
            
            if ($type == MerchantComplaint::TYPE_MERCHANT) {
                $data['merchant_id'] = $targetId ?: ($params['merchant_id'] ?? 0);
            } else {
                $data['merchant_id'] = $params['merchant_id'] ?? 0;
            }
            
            MerchantComplaint::create($data);
            return true;
        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }
}
