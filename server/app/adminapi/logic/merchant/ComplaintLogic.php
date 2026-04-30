<?php
namespace app\adminapi\logic\merchant;

use app\common\logic\BaseLogic;
use app\common\model\merchant\MerchantComplaint;

class ComplaintLogic extends BaseLogic
{
    /**
     * @notes 处理投诉
     * @param $params
     * @return bool
     */
    public static function handle($params)
    {
        try {
            $complaint = MerchantComplaint::findOrEmpty($params['id']);
            if ($complaint->isEmpty()) {
                throw new \Exception('投诉记录不存在');
            }
            $complaint->status = 1;
            $complaint->update_time = time();
            $complaint->save();
            return true;
        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 删除投诉
     * @param $id
     * @return bool
     */
    public static function del($id)
    {
        try {
            MerchantComplaint::destroy($id);
            return true;
        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }
}
