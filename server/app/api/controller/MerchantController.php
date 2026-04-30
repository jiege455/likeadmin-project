<?php

namespace app\api\controller;

use app\common\model\merchant\Merchant;
use app\common\model\user\UserFollowMerchant;
use app\api\lists\merchant\MerchantLists; // 引入新创建的列表类
use app\api\lists\merchant\FollowLists;   // 引入关注列表类
use think\facade\Db;

class MerchantController extends BaseApiController
{
    /**
     * @notes 商家列表（支持搜索）
     * by：杰哥
     */
    public function lists()
    {
        return $this->dataLists(new MerchantLists());
    }

    /**
     * 关注/取消关注商家
     */
    public function follow()
    {
        $merchantId = $this->request->post('merchant_id');
        $userId = $this->userId;

        if (empty($merchantId)) {
            return $this->fail('参数错误');
        }

        $exist = Db::name('user_follow_merchant')
            ->where(['user_id' => $userId, 'merchant_id' => $merchantId])
            ->find();

        if ($exist) {
            // 取消关注
            Db::name('user_follow_merchant')->delete($exist['id']);
            return $this->success('已取消关注');
        } else {
            // 关注
            Db::name('user_follow_merchant')->insert([
                'user_id' => $userId,
                'merchant_id' => $merchantId,
                'create_time' => time()
            ]);
            return $this->success('关注成功');
        }
    }

    /**
     * 商户详情
     */
    public function detail()
    {
        $userId = $this->userId;
        $merchant = Db::name('merchant')->where('user_id', $userId)->find();
        
        if (!$merchant) {
            return $this->fail('您还不是商户');
        }
        
        return $this->data($merchant);
    }

    /**
     * 获取指定商户信息（公开）
     */
    public function info()
    {
        $id = $this->request->get('id');
        if (empty($id)) return $this->fail('参数错误');
        
        $merchant = Db::name('merchant')
            ->field('id, name, image, logo, desc, intro, mobile, wechat, create_time, distribution_switch, distribution_ratio')
            ->where('id', $id)
            ->where('status', 1)
            ->find();
            
        if (!$merchant) return $this->fail('商户不存在');

        $merchant['image'] = $merchant['image'] ? \app\common\service\FileService::getFileUrl($merchant['image']) : '';
        $merchant['logo'] = $merchant['logo'] ? \app\common\service\FileService::getFileUrl($merchant['logo']) : '';
        
        $merchant['article_count'] = Db::name('article')
            ->where('merchant_id', $id)
            ->where('is_show', 1)
            ->count();
        
        return $this->data($merchant);
    }

    /**
     * 修改商户资料
     */
    public function edit()
    {
        $post = $this->request->post();
        $userId = $this->userId;
        $merchant = Db::name('merchant')->where('user_id', $userId)->find();

        if (!$merchant) {
            return $this->fail('您还不是商户');
        }

        $data = [];
        if (isset($post['name'])) $data['name'] = $post['name'];
        if (isset($post['image'])) $data['image'] = $post['image'];
        if (isset($post['desc'])) $data['desc'] = $post['desc'];
        
        if (empty($data)) {
            return $this->fail('没有要修改的内容');
        }

        $data['update_time'] = time();
        Db::name('merchant')->where('id', $merchant['id'])->update($data);

        return $this->success('修改成功');
    }

    /**
     * 我的关注列表
     * by：杰哥
     */
    public function followLists()
    {
        // 改用标准的 Lists 类，支持分页和搜索
        return $this->dataLists(new FollowLists());
    }
}
