<?php
namespace app\adminapi\logic\user;

use app\common\logic\BaseLogic;
use app\common\model\user\UserRealname;
use app\common\service\realname\RealNameService;
use app\common\service\ConfigService;

class UserRealnameLogic extends BaseLogic
{
    /**
     * @notes 实名记录列表
     */
    public static function lists($params)
    {
        $where = [];
        if (isset($params['keyword']) && !empty($params['keyword'])) {
            $where[] = ['real_name|id_card|mobile', 'like', '%' . $params['keyword'] . '%'];
        }
        if (isset($params['status']) && $params['status'] !== '') {
            $where[] = ['status', '=', $params['status']];
        }

        $count = UserRealname::where($where)->count();
        $lists = UserRealname::where($where)
            ->page($params['page_no'] ?? 1, $params['page_size'] ?? 10)
            ->order('id', 'desc')
            ->select()
            ->append(['status_desc'])
            ->toArray();

        return [
            'lists' => $lists,
            'count' => $count,
            'page_no' => $params['page_no'] ?? 1,
            'page_size' => $params['page_size'] ?? 10,
        ];
    }

    /**
     * @notes 审核
     */
    public static function audit($params)
    {
        $id = $params['id'];
        $status = $params['status'];
        $failReason = $params['fail_reason'] ?? '';

        $record = UserRealname::find($id);
        if (!$record) {
            self::$error = '记录不存在';
            return false;
        }

        if ($record->status != UserRealname::STATUS_WAIT) {
            self::$error = '该记录已审核';
            return false;
        }

        \think\facade\Db::startTrans();
        try {
            $record->status = $status;
            if ($status == UserRealname::STATUS_FAIL) {
                $record->fail_reason = $failReason;
            }
            $record->save();

            if ($status == UserRealname::STATUS_SUCCESS) {
                \app\common\model\user\User::update(['real_name' => $record->real_name], ['id' => $record->user_id]);
            }

            \think\facade\Db::commit();
            return true;
        } catch (\Exception $e) {
            \think\facade\Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 获取配置
     */
    public static function getConfig()
    {
        return RealNameService::getConfig();
    }

    /**
     * @notes 保存配置
     */
    public static function setConfig($params)
    {
        ConfigService::set('realname', 'status', $params['status'] ?? 0);
        ConfigService::set('realname', 'auth_type', $params['auth_type'] ?? 'manual');
        ConfigService::set('realname', 'aliyun_appcode', $params['aliyun_appcode'] ?? '');
        ConfigService::set('realname', 'aliyun_url', $params['aliyun_url'] ?? '');
        ConfigService::set('realname', 'umeng_appkey', $params['umeng_appkey'] ?? '');
        ConfigService::set('realname', 'umeng_appsecret', $params['umeng_appsecret'] ?? '');
        return true;
    }
}
