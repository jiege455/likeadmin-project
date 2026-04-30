<?php
namespace app\adminapi\logic\distribution;

use app\common\logic\BaseLogic;
use app\common\logic\EmailNotifyLogic;
use app\common\model\distribution\DistributionApply;
use app\common\model\user\User;

class DistributionApplyLogic extends BaseLogic
{
    public static function audit($params)
    {
        \think\facade\Db::startTrans();
        try {
            $apply = DistributionApply::find($params['id']);
            if (!$apply || $apply['status'] != DistributionApply::STATUS_WAIT) {
                throw new \Exception('申请不存在或已处理');
            }

            if ($params['status'] == DistributionApply::STATUS_PASS) {
                User::update(['is_distributor' => 1], ['id' => $apply['user_id']]);
            }

            $apply->status = $params['status'];
            $apply->audit_remark = $params['remark'] ?? '';
            $apply->audit_time = time();
            $apply->save();

            EmailNotifyLogic::sendDistributionAuditNotify(
                $apply->id,
                $params['status'] == DistributionApply::STATUS_PASS ? 1 : 2,
                $params['remark'] ?? ''
            );

            \think\facade\Db::commit();
            return true;
        } catch (\Exception $e) {
            \think\facade\Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 删除申请
     */
    public static function delete($params)
    {
        try {
            $apply = DistributionApply::find($params['id']);
            if (!$apply) {
                throw new \Exception('申请不存在');
            }
            
            // 如果删除的是已通过的申请，是否需要取消分销员资格？
            // 目前逻辑：仅删除申请记录，不影响用户分销员身份
            // 如果需要连带取消身份，可以打开下面的注释：
            /*
            if ($apply->status == DistributionApply::STATUS_PASS) {
                User::update(['is_distributor' => 0], ['id' => $apply['user_id']]);
            }
            */

            $apply->delete();
            return true;
        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }
}
