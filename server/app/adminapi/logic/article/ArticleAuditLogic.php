<?php
namespace app\adminapi\logic\article;

use app\common\logic\BaseLogic;
use app\common\service\FileService;
use think\facade\Db;

class ArticleAuditLogic extends BaseLogic
{
    public static function lists($get)
    {
        $where = [];
        $where[] = ['a.delete_time', '=', null];
        
        if (!empty($get['title'])) {
            $where[] = ['a.title', 'like', '%' . $get['title'] . '%'];
        }
        if (isset($get['audit_status']) && $get['audit_status'] !== '') {
            $where[] = ['a.audit_status', '=', $get['audit_status']];
        }
        if (!empty($get['merchant_id'])) {
            $where[] = ['a.merchant_id', '=', $get['merchant_id']];
        }

        $count = Db::name('article')->alias('a')->where($where)->count();
        $lists = Db::name('article')
            ->alias('a')
            ->leftJoin('article_cate c', 'a.cid = c.id')
            ->leftJoin('merchant m', 'a.merchant_id = m.id')
            ->field('a.*, c.name as cate_name, m.name as merchant_name')
            ->where($where)
            ->where('a.merchant_id', '>', 0)
            ->page($get['page_no'] ?? 1, $get['page_size'] ?? 15)
            ->order('a.id', 'desc')
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['image'] = $item['image'] ? FileService::getFileUrl($item['image']) : '';
            $item['create_time'] = date('Y-m-d H:i', $item['create_time']);
            $item['audit_status_text'] = self::getAuditStatusText($item['audit_status']);
        }

        return ['count' => $count, 'lists' => $lists];
    }

    public static function detail($id)
    {
        $article = Db::name('article')
            ->alias('a')
            ->leftJoin('article_cate c', 'a.cid = c.id')
            ->leftJoin('merchant m', 'a.merchant_id = m.id')
            ->field('a.*, c.name as cate_name, m.name as merchant_name')
            ->where('a.id', $id)
            ->find();

        if (!$article) return null;

        $article['image'] = $article['image'] ? FileService::getFileUrl($article['image']) : '';
        $article['create_time'] = date('Y-m-d H:i:s', $article['create_time']);
        $article['audit_status_text'] = self::getAuditStatusText($article['audit_status']);

        return $article;
    }

    public static function statistics()
    {
        $total = Db::name('article')->where('merchant_id', '>', 0)->where('delete_time', null)->count();
        $pending = Db::name('article')->where('merchant_id', '>', 0)->where('delete_time', null)->where('audit_status', 0)->count();
        $passed = Db::name('article')->where('merchant_id', '>', 0)->where('delete_time', null)->where('audit_status', 1)->count();
        $rejected = Db::name('article')->where('merchant_id', '>', 0)->where('delete_time', null)->where('audit_status', 2)->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'passed' => $passed,
            'rejected' => $rejected,
        ];
    }

    public static function audit($params)
    {
        Db::startTrans();
        try {
            $id = $params['id'];
            $status = $params['status'];
            $reason = $params['reason'] ?? '';

            $article = Db::name('article')->find($id);
            if (!$article) {
                throw new \Exception('文章不存在');
            }

            Db::name('article')->where('id', $id)->update([
                'audit_status' => $status,
                'audit_reason' => $reason,
                'audit_time' => time(),
                'update_time' => time()
            ]);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    public static function batchAudit($params)
    {
        Db::startTrans();
        try {
            $ids = $params['ids'] ?? [];
            $status = $params['status'];
            $reason = $params['reason'] ?? '';

            if (empty($ids)) {
                throw new \Exception('请选择文章');
            }

            Db::name('article')->whereIn('id', $ids)->update([
                'audit_status' => $status,
                'audit_reason' => $reason,
                'audit_time' => time(),
                'update_time' => time()
            ]);
            Db::commit();
            return true;
        } catch (\Exception $e) {
            Db::rollback();
            self::setError($e->getMessage());
            return false;
        }
    }

    private static function getAuditStatusText($status)
    {
        $texts = [
            0 => '待审核',
            1 => '已通过',
            2 => '已拒绝'
        ];
        return $texts[$status] ?? '未知';
    }
}
