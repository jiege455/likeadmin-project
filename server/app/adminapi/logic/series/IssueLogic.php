<?php
/**
 * 期次管理逻辑
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\adminapi\logic\series;

use app\common\logic\BaseLogic;
use think\facade\Db;

class IssueLogic extends BaseLogic
{
    public static function add($params)
    {
        $seriesId = intval($params['series_id'] ?? 0);
        $issueNo = $params['issue_no'] ?? '';
        $title = $params['title'] ?? '';
        $content = $params['content'] ?? '';

        if ($seriesId <= 0) {
            self::$error = '请选择系列';
            return false;
        }
        if (empty($issueNo)) {
            self::$error = '请输入期号';
            return false;
        }
        if (empty($title)) {
            self::$error = '请输入标题';
            return false;
        }
        if (empty($content)) {
            self::$error = '请输入正文内容';
            return false;
        }

        $series = Db::name('article_cate')->where('id', $seriesId)->where('is_series', 1)->find();
        if (!$series) {
            self::$error = '系列不存在';
            return false;
        }

        $existIssue = Db::name('article')
            ->where('series_id', $seriesId)
            ->where('issue_no', $issueNo)
            ->where('delete_time', null)
            ->find();
        if ($existIssue) {
            self::$error = '该期号已存在';
            return false;
        }

        Db::name('article')->insert([
            'cid' => $seriesId,
            'series_id' => $seriesId,
            'issue_no' => $issueNo,
            'title' => $title,
            'image' => $params['image'] ?? '',
            'desc' => $params['desc'] ?? '',
            'content' => $content,
            'hidden_content' => $params['hidden_content'] ?? '',
            'open_code' => $params['open_code'] ?? '',
            'open_time' => intval($params['open_time'] ?? 0),
            'is_show' => 1,
            'issue_status' => 0,
            'is_opened' => 0,
            'price' => 0,
            'create_time' => time(),
            'update_time' => time()
        ]);

        Db::name('article_cate')->where('id', $seriesId)->inc('total_issues')->update();

        return true;
    }

    public static function edit($params)
    {
        $id = intval($params['id'] ?? 0);
        if ($id <= 0) {
            self::$error = '参数错误';
            return false;
        }

        $exist = Db::name('article')->where('id', $id)->where('series_id', '>', 0)->find();
        if (!$exist) {
            self::$error = '期次不存在';
            return false;
        }

        if ($exist['is_opened'] == 1) {
            self::$error = '已开奖，不可编辑';
            return false;
        }

        Db::name('article')->where('id', $id)->update([
            'issue_no' => $params['issue_no'] ?? $exist['issue_no'],
            'title' => $params['title'] ?? $exist['title'],
            'image' => $params['image'] ?? $exist['image'],
            'desc' => $params['desc'] ?? $exist['desc'],
            'content' => $params['content'] ?? $exist['content'],
            'hidden_content' => $params['hidden_content'] ?? $exist['hidden_content'],
            'open_code' => $params['open_code'] ?? $exist['open_code'],
            'open_time' => intval($params['open_time'] ?? 0),
            'update_time' => time()
        ]);

        return true;
    }

    public static function detail($params)
    {
        $id = intval($params['id'] ?? 0);
        return Db::name('article')->where('id', $id)->find();
    }

    public static function delete($params)
    {
        $id = intval($params['id'] ?? 0);
        if ($id <= 0) {
            self::$error = '参数错误';
            return false;
        }

        $exist = Db::name('article')->where('id', $id)->where('series_id', '>', 0)->find();
        if (!$exist) {
            self::$error = '期次不存在';
            return false;
        }

        if ($exist['is_opened'] == 1) {
            self::$error = '已开奖，不可删除';
            return false;
        }

        Db::name('article')->where('id', $id)->update([
            'delete_time' => time()
        ]);

        Db::name('article_cate')->where('id', $exist['series_id'])->dec('total_issues')->update();

        return true;
    }

    public static function publish($params)
    {
        $id = intval($params['id'] ?? 0);
        if ($id <= 0) {
            self::$error = '参数错误';
            return false;
        }

        $exist = Db::name('article')->where('id', $id)->where('series_id', '>', 0)->find();
        if (!$exist) {
            self::$error = '期次不存在';
            return false;
        }

        if ($exist['issue_status'] != 0) {
            self::$error = '该期次已发布';
            return false;
        }

        Db::name('article')->where('id', $id)->update([
            'issue_status' => 1,
            'update_time' => time()
        ]);

        return true;
    }
}
