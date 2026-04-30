<?php
namespace app\adminapi\logic\marketing;

use app\common\logic\BaseLogic;
use think\facade\Db;

class CouponLogic extends BaseLogic
{
    public static function lists($get)
    {
        $where = [];
        $where[] = ['c.delete_time', '=', null];
        if (!empty($get['name'])) {
            $where[] = ['c.name', 'like', '%' . $get['name'] . '%'];
        }

        $count = Db::name('coupon')->alias('c')->where($where)->count();
        $lists = Db::name('coupon')
            ->alias('c')
            ->leftJoin('merchant m', 'c.merchant_id = m.id')
            ->field('c.*, m.name as merchant_name')
            ->where($where)
            ->page($get['page_no'] ?? 1, $get['page_size'] ?? 10)
            ->order('c.id', 'desc')
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['use_time_desc'] = $item['use_time_type'] == 1 
                ? date('Y-m-d H:i:s', $item['use_time_start']) . ' ~ ' . date('Y-m-d H:i:s', $item['use_time_end'])
                : '领取后' . $item['use_days'] . '天内有效';
            $item['merchant_name'] = $item['merchant_name'] ?: '平台';
        }

        return ['count' => $count, 'lists' => $lists];
    }

    public static function add($params)
    {
        $data = [
            'name' => $params['name'],
            'money' => $params['money'],
            'condition_money' => $params['condition_money'],
            'total_count' => $params['total_count'],
            'send_count' => 0,
            'use_time_type' => $params['use_time_type'],
            'status' => $params['status'],
            'create_time' => time(),
            'update_time' => time()
        ];

        if ($params['use_time_type'] == 1) {
            $data['use_time_start'] = strtotime($params['use_time_start']);
            $data['use_time_end'] = strtotime($params['use_time_end']);
        } else {
            $data['use_days'] = $params['use_days'];
        }

        Db::name('coupon')->insert($data);
    }

    public static function edit($params)
    {
        $data = [
            'name' => $params['name'],
            'money' => $params['money'],
            'condition_money' => $params['condition_money'],
            'total_count' => $params['total_count'],
            'use_time_type' => $params['use_time_type'],
            'status' => $params['status'],
            'update_time' => time()
        ];

        if ($params['use_time_type'] == 1) {
            $data['use_time_start'] = strtotime($params['use_time_start']);
            $data['use_time_end'] = strtotime($params['use_time_end']);
        } else {
            $data['use_days'] = $params['use_days'];
        }

        Db::name('coupon')->where('id', $params['id'])->update($data);
    }

    public static function del($id)
    {
        Db::name('coupon')->where('id', $id)->update(['delete_time' => time()]);
    }

    public static function detail($id)
    {
        $detail = Db::name('coupon')->find($id);
        if ($detail['use_time_type'] == 1) {
            $detail['use_time_start'] = date('Y-m-d H:i:s', $detail['use_time_start']);
            $detail['use_time_end'] = date('Y-m-d H:i:s', $detail['use_time_end']);
        }
        return $detail;
    }
}
