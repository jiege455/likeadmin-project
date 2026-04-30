<?php
/**
 * 待处理审批管理逻辑层
 * 开发者：杰哥网络科技 qq2711793818 杰哥
 */

namespace app\adminapi\logic;

use app\common\logic\BaseLogic;
use think\facade\Db;

class PendingApprovalLogic extends BaseLogic
{
    public static function getError(): string
    {
        if (false === self::hasError()) {
            return '系统错误';
        }
        return self::$error;
    }

    /**
     * @notes 获取待处理审批统计
     * @return array
     */
    public static function statistics(): array
    {
        $statistics = [];

        $statistics['distribution_apply'] = Db::name('distribution_apply')
            ->where('status', 0)
            ->whereNull('delete_time')
            ->count();

        $statistics['merchant_apply'] = Db::name('merchant_apply')
            ->where('status', 0)
            ->whereNull('delete_time')
            ->count();

        $statistics['article_audit'] = Db::name('article')
            ->where('audit_status', 0)
            ->whereNull('delete_time')
            ->count();

        $statistics['withdraw_apply'] = Db::name('withdraw_apply')
            ->where('status', 0)
            ->whereNull('delete_time')
            ->count();

        $statistics['total'] = array_sum($statistics);

        return $statistics;
    }

    /**
     * @notes 快捷审批
     * @param array $params
     * @return bool
     */
    public static function audit(array $params): bool
    {
        $type = $params['type'] ?? '';
        $id = intval($params['id'] ?? 0);
        $status = intval($params['status'] ?? 1);
        $remark = $params['remark'] ?? '';

        if (empty($type) || $id <= 0) {
            self::$error = '参数错误';
            return false;
        }

        Db::startTrans();
        try {
            switch ($type) {
                case 'distribution_apply':
                    $result = self::auditDistributionApply($id, $status, $remark);
                    break;
                case 'merchant_apply':
                    $result = self::auditMerchantApply($id, $status, $remark);
                    break;
                case 'article_audit':
                    $result = self::auditArticle($id, $status, $remark);
                    break;
                case 'withdraw_apply':
                    $result = self::auditWithdraw($id, $status, $remark);
                    break;
                default:
                    self::$error = '未知的审批类型';
                    Db::rollback();
                    return false;
            }

            if (!$result) {
                Db::rollback();
                return false;
            }

            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 审核分销员申请
     */
    private static function auditDistributionApply($id, $status, $remark): bool
    {
        $apply = Db::name('distribution_apply')
            ->where('id', $id)
            ->where('status', 0)
            ->find();

        if (!$apply) {
            self::$error = '申请不存在或已处理';
            return false;
        }

        Db::name('distribution_apply')
            ->where('id', $id)
            ->update([
                'status' => $status,
                'audit_remark' => $remark,
                'audit_time' => time(),
                'update_time' => time()
            ]);

        if ($status == 1) {
            Db::name('user')
                ->where('id', $apply['user_id'])
                ->update(['is_distributor' => 1]);
        }

        return true;
    }

    /**
     * @notes 审核商家入驻申请
     */
    private static function auditMerchantApply($id, $status, $remark): bool
    {
        $apply = Db::name('merchant_apply')
            ->where('id', $id)
            ->where('status', 0)
            ->find();

        if (!$apply) {
            self::$error = '申请不存在或已处理';
            return false;
        }

        Db::name('merchant_apply')
            ->where('id', $id)
            ->update([
                'status' => $status,
                'audit_remark' => $remark,
                'audit_time' => time(),
                'update_time' => time()
            ]);

        if ($status == 1) {
            $existMerchant = Db::name('merchant')
                ->where('user_id', $apply['user_id'])
                ->find();

            if (!$existMerchant) {
                Db::name('merchant')->insert([
                    'user_id' => $apply['user_id'],
                    'name' => $apply['name'],
                    'logo' => $apply['logo'] ?? '',
                    'desc' => $apply['desc'] ?? '',
                    'mobile' => $apply['mobile'] ?? '',
                    'email' => $apply['email'] ?? '',
                    'wechat' => $apply['wechat'] ?? '',
                    'status' => 1,
                    'create_time' => time(),
                    'update_time' => time()
                ]);
            }
        }

        return true;
    }

    /**
     * @notes 审核文章
     */
    private static function auditArticle($id, $status, $remark): bool
    {
        $article = Db::name('article')
            ->where('id', $id)
            ->where('audit_status', 0)
            ->find();

        if (!$article) {
            self::$error = '文章不存在或已审核';
            return false;
        }

        Db::name('article')
            ->where('id', $id)
            ->update([
                'audit_status' => $status,
                'audit_reason' => $remark,
                'audit_time' => time(),
                'update_time' => time()
            ]);

        return true;
    }

    /**
     * @notes 审核提现申请
     */
    private static function auditWithdraw($id, $status, $remark): bool
    {
        $withdraw = Db::name('withdraw_apply')
            ->where('id', $id)
            ->where('status', 0)
            ->find();

        if (!$withdraw) {
            self::$error = '提现申请不存在或已处理';
            return false;
        }

        if ($status == 1) {
            Db::name('withdraw_apply')
                ->where('id', $id)
                ->update([
                    'status' => 1,
                    'audit_remark' => $remark,
                    'audit_time' => time(),
                    'update_time' => time()
                ]);
        } else {
            Db::name('withdraw_apply')
                ->where('id', $id)
                ->update([
                    'status' => 2,
                    'audit_remark' => $remark,
                    'audit_time' => time(),
                    'update_time' => time()
                ]);

            if ($withdraw['source'] == 'merchant') {
                Db::name('merchant')
                    ->where('id', $withdraw['merchant_id'])
                    ->inc('balance', $withdraw['money'])
                    ->update();
            } else {
                Db::name('user')
                    ->where('id', $withdraw['user_id'])
                    ->inc('user_money', $withdraw['money'])
                    ->update();
            }
        }

        return true;
    }

    /**
     * @notes 获取审批详情
     */
    public static function detail(array $params)
    {
        $type = $params['type'] ?? '';
        $id = intval($params['id'] ?? 0);

        if (empty($type) || $id <= 0) {
            self::$error = '参数错误';
            return false;
        }

        switch ($type) {
            case 'distribution_apply':
                return self::getDistributionApplyDetail($id);
            case 'merchant_apply':
                return self::getMerchantApplyDetail($id);
            case 'article_audit':
                return self::getArticleDetail($id);
            case 'withdraw_apply':
                return self::getWithdrawDetail($id);
            default:
                self::$error = '未知的审批类型';
                return false;
        }
    }

    private static function getDistributionApplyDetail($id)
    {
        $apply = Db::name('distribution_apply')
            ->alias('da')
            ->leftJoin('user u', 'da.user_id = u.id')
            ->field('da.*, u.nickname, u.avatar, u.mobile as user_mobile')
            ->where('da.id', $id)
            ->find();

        if (!$apply) {
            self::$error = '申请不存在';
            return false;
        }

        $apply['status_text'] = [0 => '待审核', 1 => '已通过', 2 => '已拒绝'][$apply['status']] ?? '未知';
        $apply['create_time_text'] = date('Y-m-d H:i:s', $apply['create_time']);

        return $apply;
    }

    private static function getMerchantApplyDetail($id)
    {
        $apply = Db::name('merchant_apply')
            ->alias('ma')
            ->leftJoin('user u', 'ma.user_id = u.id')
            ->field('ma.*, u.nickname, u.avatar, u.mobile as user_mobile')
            ->where('ma.id', $id)
            ->find();

        if (!$apply) {
            self::$error = '申请不存在';
            return false;
        }

        $apply['status_text'] = [0 => '待审核', 1 => '已通过', 2 => '已拒绝'][$apply['status']] ?? '未知';
        $apply['create_time_text'] = date('Y-m-d H:i:s', $apply['create_time']);

        return $apply;
    }

    private static function getArticleDetail($id)
    {
        $article = Db::name('article')
            ->alias('a')
            ->leftJoin('user u', 'a.user_id = u.id')
            ->leftJoin('merchant m', 'a.merchant_id = m.id')
            ->field('a.*, u.nickname as author_name, m.name as merchant_name')
            ->where('a.id', $id)
            ->find();

        if (!$article) {
            self::$error = '文章不存在';
            return false;
        }

        $article['audit_status_text'] = [0 => '待审核', 1 => '已通过', 2 => '已拒绝'][$article['audit_status']] ?? '未知';
        $article['create_time_text'] = date('Y-m-d H:i:s', $article['create_time']);

        return $article;
    }

    private static function getWithdrawDetail($id)
    {
        $withdraw = Db::name('withdraw_apply')
            ->alias('wa')
            ->leftJoin('user u', 'wa.user_id = u.id')
            ->leftJoin('merchant m', 'wa.merchant_id = m.id')
            ->field('wa.*, u.nickname as user_name, m.name as merchant_name')
            ->where('wa.id', $id)
            ->find();

        if (!$withdraw) {
            self::$error = '提现申请不存在';
            return false;
        }

        $withdraw['status_text'] = [0 => '待审核', 1 => '已拒绝', 2 => '已通过', 3 => '已打款'][$withdraw['status']] ?? '未知';
        $withdraw['create_time_text'] = date('Y-m-d H:i:s', $withdraw['create_time']);
        $withdraw['source_text'] = $withdraw['source'] == 'merchant' ? '商家提现' : '推广员提现';

        return $withdraw;
    }
}
