<?php
/**
 * 系列管理控制器
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\api\controller\merchant;

use app\api\controller\BaseApiController;
use think\facade\Db;

class SeriesController extends BaseApiController
{
    public function lists()
    {
        $userId = $this->userId;
        
        $merchant = Db::name('merchant')->where('user_id', $userId)->find();
        if (!$merchant) {
            return $this->fail('您还不是商户');
        }

        $where = [
            ['is_series', '=', 1],
            ['delete_time', '=', null],
            ['series_status', '=', 1]
        ];

        $lists = Db::name('article_cate')
            ->where($where)
            ->where(function($query) use ($merchant) {
                $query->where('merchant_id', 0)
                      ->whereOr('merchant_id', $merchant['id']);
            })
            ->order('create_time', 'desc')
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['published_issues'] = Db::name('article')
                ->where('series_id', $item['id'])
                ->where('delete_time', null)
                ->count();
        }

        return $this->data([
            'lists' => $lists
        ]);
    }

    public function detail()
    {
        $userId = $this->userId;
        $id = $this->request->get('id', 0);
        
        $merchant = Db::name('merchant')->where('user_id', $userId)->find();
        if (!$merchant) {
            return $this->fail('您还不是商户');
        }

        $series = Db::name('article_cate')
            ->where('id', $id)
            ->where('is_series', 1)
            ->where('delete_time', null)
            ->where(function($query) use ($merchant) {
                $query->where('merchant_id', 0)
                      ->whereOr('merchant_id', $merchant['id']);
            })
            ->find();

        if (!$series) {
            return $this->fail('系列不存在');
        }

        return $this->data($series);
    }

    public function save()
    {
        $userId = $this->userId;
        $params = $this->request->post();
        
        $merchant = Db::name('merchant')->where('user_id', $userId)->find();
        if (!$merchant) {
            return $this->fail('您还不是商户');
        }

        if (empty($params['name'])) {
            return $this->fail('请输入系列名称');
        }

        if (empty($params['lottery_type'])) {
            return $this->fail('请选择彩票类型');
        }

        $data = [
            'name' => $params['name'],
            'lottery_type' => $params['lottery_type'] ?? '',
            'series_price' => floatval($params['series_price'] ?? 0),
            'total_issues' => intval($params['total_issues'] ?? 0),
            'series_desc' => $params['series_desc'] ?? '',
            'auto_publish' => intval($params['auto_publish'] ?? 0),
            'publish_interval' => intval($params['publish_interval'] ?? 0),
            'series_status' => intval($params['series_status'] ?? 1),
            'is_series' => 1,
            'is_show' => 1,
            'merchant_id' => $merchant['id'],
            'update_time' => time()
        ];

        if (empty($params['id'])) {
            $data['create_time'] = time();
            Db::name('article_cate')->insert($data);
        } else {
            $series = Db::name('article_cate')
                ->where('id', $params['id'])
                ->where('merchant_id', $merchant['id'])
                ->where('is_series', 1)
                ->find();
            
            if (!$series) {
                return $this->fail('系列不存在或无权编辑');
            }
            
            Db::name('article_cate')->where('id', $params['id'])->update($data);
        }

        return $this->success('保存成功');
    }

    public function delete()
    {
        $userId = $this->userId;
        $id = $this->request->post('id', 0);
        
        $merchant = Db::name('merchant')->where('user_id', $userId)->find();
        if (!$merchant) {
            return $this->fail('您还不是商户');
        }

        $series = Db::name('article_cate')
            ->where('id', $id)
            ->where('merchant_id', $merchant['id'])
            ->where('is_series', 1)
            ->find();

        if (!$series) {
            return $this->fail('系列不存在或无权删除');
        }

        $issueCount = Db::name('article')
            ->where('series_id', $id)
            ->where('delete_time', null)
            ->count();

        if ($issueCount > 0) {
            return $this->fail('该系列下还有文章，请先删除文章');
        }

        Db::name('article_cate')->where('id', $id)->update(['delete_time' => time()]);

        return $this->success('删除成功');
    }

    public function status()
    {
        $userId = $this->userId;
        $id = $this->request->post('id', 0);
        $status = $this->request->post('status', 0);
        
        $merchant = Db::name('merchant')->where('user_id', $userId)->find();
        if (!$merchant) {
            return $this->fail('您还不是商户');
        }

        $series = Db::name('article_cate')
            ->where('id', $id)
            ->where('merchant_id', $merchant['id'])
            ->where('is_series', 1)
            ->find();

        if (!$series) {
            return $this->fail('系列不存在或无权操作');
        }

        Db::name('article_cate')->where('id', $id)->update([
            'series_status' => $status,
            'update_time' => time()
        ]);

        return $this->success('操作成功');
    }

    public function lastIssue()
    {
        $userId = $this->userId;
        $seriesId = $this->request->get('series_id', 0);
        $issueNo = $this->request->get('issue_no', '');
        
        $merchant = Db::name('merchant')->where('user_id', $userId)->find();
        if (!$merchant) {
            return $this->fail('您还不是商户');
        }

        if ($seriesId <= 0) {
            return $this->data([
                'issue_no' => '',
                'hidden_content' => '',
                'prev_issue_no' => '',
                'prev_issue_content' => ''
            ]);
        }

        $query = Db::name('article')
            ->where('series_id', $seriesId)
            ->where('delete_time', null);
        
        if (!empty($issueNo)) {
            $query->where('issue_no', $issueNo);
        } else {
            $query->order('create_time', 'desc');
        }
        
        $lastIssue = $query->find();

        if (!$lastIssue) {
            return $this->data([
                'issue_no' => '',
                'hidden_content' => '',
                'prev_issue_no' => '',
                'prev_issue_content' => ''
            ]);
        }

        return $this->data([
            'issue_no' => $lastIssue['issue_no'] ?? '',
            'hidden_content' => $lastIssue['hidden_content'] ?? '',
            'prev_issue_no' => $lastIssue['issue_no'] ?? '',
            'prev_issue_content' => $lastIssue['hidden_content'] ?? ''
        ]);
    }
}
