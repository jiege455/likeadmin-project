<?php
namespace app\api\logic\series;

use app\common\logic\BaseLogic;
use think\facade\Db;

class IssueLogic extends BaseLogic
{
    public static function read($id, $userId = 0)
    {
        $issue = Db::name('article')
            ->where('id', $id)
            ->where('series_id', '>', 0)
            ->where('issue_status', '>', 0)
            ->where('delete_time', null)
            ->find();

        if (!$issue) {
            return ['info' => null, 'has_permission' => false];
        }

        $series = Db::name('article_cate')
            ->where('id', $issue['series_id'])
            ->find();

        $hasPermission = false;
        if ($userId > 0) {
            $permission = Db::name('user_series_permission')
                ->where('user_id', $userId)
                ->where('series_id', $issue['series_id'])
                ->where(function($query) {
                    $query->whereNull('expire_time')->whereOr('expire_time', '>', time());
                })
                ->find();
            $hasPermission = !empty($permission);

            if (!$hasPermission) {
                $currentIssueNo = $issue['issue_no'] ?? '';
                $buyWhere = [
                    'user_id' => $userId,
                    'article_id' => $id,
                    'pay_status' => 1
                ];
                if ($currentIssueNo !== '') {
                    $buyWhere['issue_no'] = $currentIssueNo;
                }
                $order = Db::name('article_order')->where($buyWhere)->find();
                $hasPermission = !empty($order);
            }
        }

        $issue['lottery_type'] = $series['lottery_type'] ?? '';

        if (!$hasPermission) {
            $issue['hidden_content'] = '';
        }

        return [
            'info' => $issue,
            'has_permission' => $hasPermission
        ];
    }
}
