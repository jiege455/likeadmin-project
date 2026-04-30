<?php
namespace app\api\lists\merchant;

use app\api\lists\BaseApiDataLists;
use app\common\model\merchant\MerchantFollow;

/**
 * 关注列表
 * by：杰哥
 */
class FollowLists extends BaseApiDataLists
{
    public function lists(): array
    {
        $where = [];
        if (isset($this->params['keyword']) && $this->params['keyword']) {
            $where[] = ['m.name|m.desc', 'like', '%' . $this->params['keyword'] . '%'];
        }

        $lists = MerchantFollow::alias('f')
            ->join('merchant m', 'm.id = f.merchant_id')
            ->where('f.user_id', $this->userId)
            ->where('m.status', 1)
            ->where($where)
            ->field('f.id, f.merchant_id, f.create_time, f.push_enable, m.name, m.desc, m.image, m.logo')
            ->order('f.id', 'desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        foreach ($lists as &$item) {
            $item['is_follow'] = 1;
            $item['push_enable'] = isset($item['push_enable']) ? (int)$item['push_enable'] : 1;
            $item['image'] = $item['image'] ? \app\common\service\FileService::getFileUrl($item['image']) : '';
            $item['logo'] = $item['logo'] ? \app\common\service\FileService::getFileUrl($item['logo']) : '';
        }

        return $lists;
    }

    public function count(): int
    {
        $where = [];
        if (isset($this->params['keyword']) && $this->params['keyword']) {
            $where[] = ['m.name|m.desc', 'like', '%' . $this->params['keyword'] . '%'];
        }

        return MerchantFollow::alias('f')
            ->join('merchant m', 'm.id = f.merchant_id')
            ->where('f.user_id', $this->userId)
            ->where('m.status', 1)
            ->where($where)
            ->count();
    }
}
