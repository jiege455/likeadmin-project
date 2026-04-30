<?php
/**
 * 系列列表
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\adminapi\lists\series;

use app\adminapi\lists\BaseAdminDataLists;
use think\facade\Db;

class SeriesLists extends BaseAdminDataLists
{
    public function lists(): array
    {
        $where = [];
        $where[] = ['is_series', '=', 1];
        $where[] = ['delete_time', '=', null];

        if (!empty($this->params['keyword'])) {
            $where[] = ['name', 'like', '%' . $this->params['keyword'] . '%'];
        }
        if (!empty($this->params['lottery_type'])) {
            $where[] = ['lottery_type', '=', $this->params['lottery_type']];
        }
        if (isset($this->params['series_status']) && $this->params['series_status'] !== '') {
            $where[] = ['series_status', '=', $this->params['series_status']];
        }

        $lists = Db::name('article_cate')
            ->where($where)
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->page($this->pageNo, $this->pageSize)
            ->select()
            ->toArray();

        // 批量查询已发布期数（修复N+1查询）
        $seriesIds = array_column($lists, 'id');
        $publishedIssuesMap = [];
        if (!empty($seriesIds)) {
            $publishedCounts = Db::name('article')
                ->whereIn('series_id', $seriesIds)
                ->where('issue_status', '>', 0)
                ->where('delete_time', null)
                ->group('series_id')
                ->column('COUNT(*) as count', 'series_id');
            $publishedIssuesMap = $publishedCounts;
        }

        foreach ($lists as &$item) {
            $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
            $item['published_issues'] = $publishedIssuesMap[$item['id']] ?? 0;
        }

        return $lists;
    }

    public function count(): int
    {
        $where = [];
        $where[] = ['is_series', '=', 1];
        $where[] = ['delete_time', '=', null];

        if (!empty($this->params['keyword'])) {
            $where[] = ['name', 'like', '%' . $this->params['keyword'] . '%'];
        }
        if (!empty($this->params['lottery_type'])) {
            $where[] = ['lottery_type', '=', $this->params['lottery_type']];
        }
        if (isset($this->params['series_status']) && $this->params['series_status'] !== '') {
            $where[] = ['series_status', '=', $this->params['series_status']];
        }

        return Db::name('article_cate')->where($where)->count();
    }
}
