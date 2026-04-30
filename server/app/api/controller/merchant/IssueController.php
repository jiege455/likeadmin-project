<?php
/**
 * 期次管理控制器
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\api\controller\merchant;

use app\api\controller\BaseApiController;
use app\api\validate\series\IssueValidate;
use app\common\model\series\Series;
use app\common\model\series\Issue;
use think\facade\Db;

class IssueController extends BaseApiController
{
    public function lists()
    {
        $params = $this->request->get();
        $userId = $this->userId;
        
        $merchant = Db::name('merchant')->where('user_id', $userId)->find();
        if (!$merchant) {
            return $this->fail('您还不是商户');
        }

        $seriesId = $params['series_id'] ?? 0;
        if ($seriesId <= 0) {
            return $this->fail('请选择系列');
        }

        $series = Series::where('id', $seriesId)
            ->where('merchant_id', $merchant['id'])
            ->find();

        if (!$series) {
            return $this->fail('系列不存在');
        }

        $where = [
            ['series_id', '=', $seriesId],
            ['delete_time', '=', null]
        ];

        if (isset($params['issue_status']) && $params['issue_status'] !== '') {
            $where[] = ['issue_status', '=', $params['issue_status']];
        }

        $pageNo = $params['page_no'] ?? 1;
        $pageSize = $params['page_size'] ?? 10;

        $lists = Issue::where($where)
            ->order('issue_no', 'desc')
            ->page($pageNo, $pageSize)
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['create_time_text'] = date('Y-m-d H:i:s', $item['create_time']);
        }

        $count = Issue::where($where)->count();

        return $this->data([
            'lists' => $lists,
            'count' => $count
        ]);
    }

    public function detail()
    {
        $params = $this->request->get();
        $userId = $this->userId;
        
        $merchant = Db::name('merchant')->where('user_id', $userId)->find();
        if (!$merchant) {
            return $this->fail('您还不是商户');
        }

        $id = $params['id'] ?? 0;
        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $issue = Issue::where('id', $id)
            ->where('delete_time', null)
            ->find();

        if (!$issue) {
            return $this->fail('期次不存在');
        }

        $issue = $issue->toArray();
        $issue['create_time_text'] = date('Y-m-d H:i:s', $issue['create_time']);

        return $this->data($issue);
    }

    public function save()
    {
        $params = $this->request->post();
        $userId = $this->userId;
        
        $merchant = Db::name('merchant')->where('user_id', $userId)->find();
        if (!$merchant) {
            return $this->fail('您还不是商户');
        }

        $validate = new IssueValidate();
        if (!$validate->scene(empty($params['id']) ? 'add' : 'edit')->check($params)) {
            return $this->fail($validate->getError());
        }

        if (empty($params['id'])) {
            $series = Series::where('id', $params['series_id'])
                ->where('merchant_id', $merchant['id'])
                ->find();

            if (!$series) {
                return $this->fail('系列不存在');
            }

            $existIssue = Issue::where('series_id', $params['series_id'])
                ->where('issue_no', $params['issue_no'])
                ->where('delete_time', null)
                ->find();

            if ($existIssue) {
                return $this->fail('期号已存在');
            }

            $data = [
                'series_id' => $params['series_id'],
                'issue_no' => $params['issue_no'],
                'title' => $params['title'],
                'content' => $params['content'],
                'open_code' => $params['open_code'] ?? '',
                'is_opened' => intval($params['is_opened'] ?? 0),
                'issue_status' => intval($params['issue_status'] ?? 0),
                'merchant_id' => $merchant['id'],
                'cid' => 0,
                'is_show' => 1,
                'create_time' => time(),
                'update_time' => time()
            ];

            Issue::insert($data);
        } else {
            $issue = Issue::where('id', $params['id'])
                ->where('merchant_id', $merchant['id'])
                ->find();

            if (!$issue) {
                return $this->fail('期次不存在');
            }

            $data = [
                'issue_no' => $params['issue_no'],
                'title' => $params['title'],
                'content' => $params['content'],
                'open_code' => $params['open_code'] ?? '',
                'is_opened' => intval($params['is_opened'] ?? 0),
                'update_time' => time()
            ];

            if (isset($params['issue_status'])) {
                $data['issue_status'] = intval($params['issue_status']);
            }

            Issue::where('id', $params['id'])->update($data);
        }

        return $this->success('保存成功');
    }

    public function delete()
    {
        $params = $this->request->post();
        $userId = $this->userId;
        
        $merchant = Db::name('merchant')->where('user_id', $userId)->find();
        if (!$merchant) {
            return $this->fail('您还不是商户');
        }

        $id = $params['id'] ?? 0;
        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $issue = Issue::where('id', $id)
            ->where('merchant_id', $merchant['id'])
            ->find();

        if (!$issue) {
            return $this->fail('期次不存在');
        }

        Issue::where('id', $id)->update(['delete_time' => time()]);

        return $this->success('删除成功');
    }

    public function publish()
    {
        $params = $this->request->post();
        $userId = $this->userId;
        
        $merchant = Db::name('merchant')->where('user_id', $userId)->find();
        if (!$merchant) {
            return $this->fail('您还不是商户');
        }

        $id = $params['id'] ?? 0;
        if ($id <= 0) {
            return $this->fail('参数错误');
        }

        $issue = Issue::where('id', $id)
            ->where('merchant_id', $merchant['id'])
            ->find();

        if (!$issue) {
            return $this->fail('期次不存在');
        }

        Issue::where('id', $id)->update([
            'issue_status' => 1,
            'update_time' => time()
        ]);

        return $this->success('发布成功');
    }
}
