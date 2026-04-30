<?php
namespace app\api\logic\series;

use app\common\logic\BaseLogic;
use think\facade\Db;

class SeriesLogic extends BaseLogic
{
    public static function lists($params)
    {
        $where = [];
        $where[] = ['is_series', '=', 1];
        $where[] = ['series_status', '=', 1];
        $where[] = ['delete_time', '=', null];

        $lotteryType = $params['lottery_type'] ?? '';
        if ($lotteryType) {
            $where[] = ['lottery_type', '=', $lotteryType];
        }

        $lists = Db::name('article_cate')
            ->where($where)
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->page($params['page_no'] ?? 1, $params['page_size'] ?? 10)
            ->select()
            ->toArray();

        return ['lists' => $lists];
    }

    public static function detail($id, $userId = 0)
    {
        $series = Db::name('article_cate')
            ->where('id', $id)
            ->where('is_series', 1)
            ->where('delete_time', null)
            ->find();

        if (!$series) {
            return null;
        }

        $issues = Db::name('article')
            ->where('series_id', $id)
            ->where('issue_status', '>', 0)
            ->where('delete_time', null)
            ->order(['issue_no' => 'desc'])
            ->field('id, issue_no, title, issue_status, is_opened, open_code, create_time')
            ->select()
            ->toArray();

        $hasPermission = false;
        if ($userId > 0) {
            $permission = Db::name('user_series_permission')
                ->where('user_id', $userId)
                ->where('series_id', $id)
                ->where(function($query) {
                    $query->whereNull('expire_time')->whereOr('expire_time', '>', time());
                })
                ->find();
            $hasPermission = !empty($permission);
        }

        return [
            'info' => $series,
            'issues' => $issues,
            'has_permission' => $hasPermission
        ];
    }
}
