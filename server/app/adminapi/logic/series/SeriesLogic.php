<?php
/**
 * 系列管理逻辑
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\adminapi\logic\series;

use app\common\logic\BaseLogic;
use think\facade\Db;

class SeriesLogic extends BaseLogic
{
    public static function add($params)
    {
        $name = $params['name'] ?? '';
        $lotteryType = $params['lottery_type'] ?? '';
        $totalIssues = intval($params['total_issues'] ?? 0);
        $seriesDesc = $params['series_desc'] ?? '';
        $autoPublish = intval($params['auto_publish'] ?? 0);
        $publishInterval = intval($params['publish_interval'] ?? 0);
        $seriesStatus = intval($params['series_status'] ?? 1);

        if (empty($name)) {
            self::$error = '请输入系列名称';
            return false;
        }
        if (empty($lotteryType)) {
            self::$error = '请选择彩票类型';
            return false;
        }

        Db::name('article_cate')->insert([
            'name' => $name,
            'lottery_type' => $lotteryType,
            'total_issues' => $totalIssues,
            'series_desc' => $seriesDesc,
            'auto_publish' => $autoPublish,
            'publish_interval' => $publishInterval,
            'series_status' => $seriesStatus,
            'is_series' => 1,
            'is_show' => 1,
            'sort' => 0,
            'create_time' => time(),
            'update_time' => time()
        ]);

        return true;
    }

    public static function edit($params)
    {
        $id = intval($params['id'] ?? 0);
        if ($id <= 0) {
            self::$error = '参数错误';
            return false;
        }

        $exist = Db::name('article_cate')->where('id', $id)->where('is_series', 1)->find();
        if (!$exist) {
            self::$error = '系列不存在';
            return false;
        }

        Db::name('article_cate')->where('id', $id)->update([
            'name' => $params['name'] ?? $exist['name'],
            'lottery_type' => $params['lottery_type'] ?? $exist['lottery_type'],
            'total_issues' => intval($params['total_issues'] ?? $exist['total_issues']),
            'series_desc' => $params['series_desc'] ?? $exist['series_desc'],
            'auto_publish' => intval($params['auto_publish'] ?? $exist['auto_publish']),
            'publish_interval' => intval($params['publish_interval'] ?? $exist['publish_interval']),
            'series_status' => intval($params['series_status'] ?? $exist['series_status']),
            'update_time' => time()
        ]);

        return true;
    }

    public static function detail($params)
    {
        $id = intval($params['id'] ?? 0);
        return Db::name('article_cate')->where('id', $id)->where('is_series', 1)->find();
    }

    public static function delete($params)
    {
        $id = intval($params['id'] ?? 0);
        if ($id <= 0) {
            self::$error = '参数错误';
            return false;
        }

        $exist = Db::name('article_cate')->where('id', $id)->where('is_series', 1)->find();
        if (!$exist) {
            self::$error = '系列不存在';
            return false;
        }

        $issueCount = Db::name('article')->where('series_id', $id)->count();
        if ($issueCount > 0) {
            self::$error = '该系列下有期次，请先删除期次';
            return false;
        }

        Db::name('article_cate')->where('id', $id)->update([
            'delete_time' => time()
        ]);

        return true;
    }

    public static function status($params)
    {
        $id = intval($params['id'] ?? 0);
        $status = intval($params['status'] ?? 0);

        if ($id <= 0) {
            self::$error = '参数错误';
            return false;
        }

        Db::name('article_cate')->where('id', $id)->update([
            'series_status' => $status,
            'update_time' => time()
        ]);

        return true;
    }
}
