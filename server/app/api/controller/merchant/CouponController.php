<?php
namespace app\api\controller\merchant;

use app\api\controller\BaseApiController;
use think\facade\Db;

class CouponController extends BaseApiController
{
    /**
     * 优惠券列表
     */
    public function lists()
    {
        $userId = $this->userId;
        $merchant = Db::name('merchant')->where('user_id', $userId)->find();
        
        if (!$merchant) {
            return $this->fail('您还不是商户');
        }

        $lists = Db::name('coupon')
            ->where('merchant_id', $merchant['id'])
            ->where('delete_time', null)
            ->order('create_time', 'desc')
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['use_time_desc'] = $item['use_time_type'] == 1 
                ? date('Y-m-d H:i', $item['use_time_start']) . ' ~ ' . date('Y-m-d H:i', $item['use_time_end'])
                : '领取后' . $item['use_days'] . '天内有效';
        }

        return $this->data($lists);
    }

    /**
     * 详情
     */
    public function detail()
    {
        $id = $this->request->get('id');
        $userId = $this->userId;
        $merchant = Db::name('merchant')->where('user_id', $userId)->find();
        
        if (!$merchant) return $this->fail('无权操作');

        $detail = Db::name('coupon')
            ->where('id', $id)
            ->where('merchant_id', $merchant['id'])
            ->find();

        if ($detail && $detail['use_time_type'] == 1) {
            $detail['use_time_start'] = date('Y-m-d H:i:s', $detail['use_time_start']);
            $detail['use_time_end'] = date('Y-m-d H:i:s', $detail['use_time_end']);
        }

        return $this->data($detail);
    }

    /**
     * 保存优惠券
     */
    public function save()
    {
        $post = $this->request->post();
        $userId = $this->userId;
        $merchant = Db::name('merchant')->where('user_id', $userId)->find();

        if (!$merchant) {
            return $this->fail('您还不是商户');
        }
        
        // 验证
        if (empty($post['name'])) return $this->fail('请输入优惠券名称');
        if (empty($post['money']) || $post['money'] <= 0) return $this->fail('请输入有效的优惠金额');
        if (empty($post['total_count']) || $post['total_count'] <= 0) return $this->fail('请输入有效的发放总量');

        $data = [
            'merchant_id' => $merchant['id'],
            'name' => $post['name'],
            'money' => $post['money'],
            'condition_money' => $post['condition_money'] ?? 0,
            'total_count' => $post['total_count'],
            'use_time_type' => $post['use_time_type'] ?? 1,
            'use_days' => $post['use_days'] ?? 1,
            'status' => $post['status'] ?? 1,
            'update_time' => time()
        ];

        if ($data['use_time_type'] == 1) {
            if (empty($post['use_time_start']) || empty($post['use_time_end'])) {
                return $this->fail('请选择有效期时间');
            }
            $data['use_time_start'] = strtotime($post['use_time_start']);
            $data['use_time_end'] = strtotime($post['use_time_end']);
        }

        if (empty($post['id'])) {
            $data['create_time'] = time();
            $data['send_count'] = 0;
            Db::name('coupon')->insert($data);
        } else {
            Db::name('coupon')
                ->where('id', $post['id'])
                ->where('merchant_id', $merchant['id'])
                ->update($data);
        }

        return $this->success('保存成功');
    }

    /**
     * 删除
     */
    public function del()
    {
        $id = $this->request->post('id');
        $userId = $this->userId;
        $merchant = Db::name('merchant')->where('user_id', $userId)->find();
        
        if (!$merchant) return $this->fail('无权操作');

        Db::name('coupon')
            ->where('id', $id)
            ->where('merchant_id', $merchant['id'])
            ->update(['delete_time' => time()]);

        return $this->success('删除成功');
    }
}
