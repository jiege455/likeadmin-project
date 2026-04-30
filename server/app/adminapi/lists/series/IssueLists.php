<?php
/**
 * 期次列表
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\adminapi\lists\series;

use app\adminapi\lists\BaseAdminDataLists;
use think\facade\Db;

class IssueLists extends BaseAdminDataLists
{
    public function lists(): array
    {
        $where = [];
        $where[] = ['delete_time', '=', null];

        if (!empty($this->params['series_id'])) {
            $where[] = ['series_id', '=', $this->params['series_id']];
        }
        if (!empty($this->params['issue_no'])) {
            $where[] = ['issue_no', 'like', '%' . $this->params['issue_no'] . '%'];
        }
        if (!empty($this->params['keyword'])) {
            $where[] = ['title', 'like', '%' . $this->params['keyword'] . '%'];
        }
        if (isset($this->params['issue_status']) && $this->params['issue_status'] !== '') {
            $where[] = ['issue_status', '=', $this->params['issue_status']];
        }

        $lists = Db::name('article')
            ->where($where)
            ->where('series_id', '>', 0)
            ->order(['id' => 'desc'])
            ->page($this->pageNo, $this->pageSize)
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
            $series = Db::name('article_cate')->where('id', $item['series_id'])->find();
            $item['series_name'] = $series ? $series['name'] : '';
        }

        return $lists;
    }

    public function count(): int
    {
        $where = [];
        $where[] = ['delete_time', '=', null];

        if (!empty($this->params['series_id'])) {
            $where[] = ['series_id', '=', $this->params['series_id']];
        }
        if (!empty($this->params['issue_no'])) {
            $where[] = ['issue_no', 'like', '%' . $this->params['issue_no'] . '%'];
        }
        if (!empty($this->params['keyword'])) {
            $where[] = ['title', 'like', '%' . $this->params['keyword'] . '%'];
        }
        if (isset($this->params['issue_status']) && $this->params['issue_status'] !== '') {
            $where[] = ['issue_status', '=', $this->params['issue_status']];
        }

        return Db::name('article')->where($where)->where('series_id', '>', 0)->count();
    }
}
