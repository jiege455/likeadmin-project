<?php
namespace app\adminapi\logic\finance;

use app\common\logic\BaseLogic;
use think\facade\Db;

class WithdrawAccountLogic extends BaseLogic
{
    public static function lists($params)
    {
        $where = [];
        $where[] = ['a.delete_time', '=', null];

        if (!empty($params['keyword'])) {
            $where[] = ['a.account|m.name|u.nickname', 'like', '%' . $params['keyword'] . '%'];
        }
        if (isset($params['type']) && $params['type'] !== '') {
            $where[] = ['a.type', '=', $params['type']];
        }
        if (isset($params['source']) && $params['source'] !== '') {
            if ($params['source'] == 1) {
                $where[] = ['a.merchant_id', '>', 0];
            } else {
                $where[] = ['a.user_id', '>', 0];
            }
        }

        $count = Db::name('withdraw_account')->alias('a')->where($where)->count();
        $lists = Db::name('withdraw_account')
            ->alias('a')
            ->leftJoin('merchant m', 'a.merchant_id = m.id')
            ->leftJoin('user u', 'a.user_id = u.id')
            ->field('a.*, m.name as merchant_name, u.nickname, u.mobile as user_mobile')
            ->where($where)
            ->order('a.id', 'desc')
            ->page($params['page_no'] ?? 1, $params['page_size'] ?? 15)
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['source_text'] = $item['merchant_id'] > 0 ? '商户' : '推广员';
            $item['type_text'] = $item['type'] == 2 ? '支付宝' : '银行卡';
            $item['status_text'] = $item['status'] == 1 ? '启用' : '禁用';
            $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
        }

        return [
            'lists' => $lists,
            'count' => $count,
            'page_no' => $params['page_no'] ?? 1,
            'page_size' => $params['page_size'] ?? 15,
        ];
    }

    public static function detail($id)
    {
        $account = Db::name('withdraw_account')
            ->alias('a')
            ->leftJoin('merchant m', 'a.merchant_id = m.id')
            ->leftJoin('user u', 'a.user_id = u.id')
            ->field('a.*, m.name as merchant_name, u.nickname, u.mobile as user_mobile')
            ->where('a.id', $id)
            ->where('a.delete_time', null)
            ->find();

        if ($account) {
            $account['source_text'] = $account['merchant_id'] > 0 ? '商户' : '推广员';
            $account['type_text'] = $account['type'] == 2 ? '支付宝' : '银行卡';
            $account['create_time'] = date('Y-m-d H:i:s', $account['create_time']);
        }

        return $account;
    }

    public static function setStatus($params)
    {
        $id = intval($params['id'] ?? 0);
        $status = intval($params['status'] ?? 0);

        $account = Db::name('withdraw_account')
            ->where('id', $id)
            ->where('delete_time', null)
            ->find();

        if (!$account) {
            self::$error = '账户不存在';
            return false;
        }

        Db::name('withdraw_account')->where('id', $id)->update([
            'status' => $status,
            'update_time' => time()
        ]);

        return true;
    }
}
