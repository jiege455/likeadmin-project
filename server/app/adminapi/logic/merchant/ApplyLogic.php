<?php
namespace app\adminapi\logic\merchant;

use app\common\logic\BaseLogic;
use app\common\logic\EmailNotifyLogic;
use app\common\model\merchant\MerchantApply;
use app\common\model\merchant\Merchant;
use think\facade\Db;

class ApplyLogic extends BaseLogic
{
    public static function audit($params)
    {
        Db::startTrans();
        try {
            $apply = MerchantApply::find($params['id']);
            if (!$apply) {
                throw new \Exception('申请记录不存在');
            }

            if ($apply->status != 0) {
                throw new \Exception('该申请已处理');
            }

            $apply->status = $params['status'];
            $apply->audit_remark = $params['audit_remark'] ?? '';
            $apply->update_time = time();
            $apply->save();

            if ($params['status'] == 1) {
                $exist = Merchant::where('user_id', $apply->user_id)->find();
                if (!$exist) {
                    Merchant::create([
                        'user_id' => $apply->user_id,
                        'name' => $apply->name,
                        'mobile' => $apply->mobile,
                        'email' => $apply->email ?? '',
                        'wechat' => $apply->wechat ?? '',
                        'desc' => $apply->desc,
                        'intro' => $apply->desc ?? '',
                        'status' => 1,
                        'create_time' => time()
                    ]);
                }
            }

            Db::commit();

            try {
                EmailNotifyLogic::sendMerchantAuditNotify(
                    $params['id'],
                    $params['status'],
                    $params['audit_remark'] ?? ''
                );
            } catch (\Exception $e) {
                \think\facade\Log::error('Merchant Audit Email Notify Error: ' . $e->getMessage());
            }

            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    public static function delete($params)
    {
        try {
            $id = $params['id'] ?? 0;
            $apply = MerchantApply::find($id);
            if (!$apply) {
                throw new \Exception('记录不存在');
            }
            $apply->delete();
            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }
}
