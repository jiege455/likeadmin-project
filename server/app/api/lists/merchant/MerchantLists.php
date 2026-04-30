<?php
namespace app\api\lists\merchant;

use app\api\lists\BaseApiDataLists;
use app\common\model\merchant\Merchant;
use app\common\model\merchant\MerchantFollow;

/**
 * 商家列表（支持搜索和关注状态）
 * by：杰哥
 */
class MerchantLists extends BaseApiDataLists
{
    /**
     * @notes 获取商家列表
     * @return array
     */
    public function lists(): array
    {
        $where = [
            ['m.status', '=', 1]
        ];

        // 关键词搜索（搜索商户名称、简介）
        if (isset($this->params['keyword']) && $this->params['keyword']) {
            $where[] = ['m.name|m.desc', 'like', '%' . $this->params['keyword'] . '%'];
        }

        $lists = Merchant::alias('m')
            ->where($where)
            ->field('m.id, m.name, m.desc, m.image, m.logo, m.create_time')
            ->order('m.id', 'desc')
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();

        // 获取当前用户关注的商家ID列表，避免循环查询数据库
        $followedMerchantIds = MerchantFollow::where('user_id', $this->userId)
            ->column('merchant_id');

        foreach ($lists as &$item) {
            // 判断是否已关注
            $item['is_follow'] = in_array($item['id'], $followedMerchantIds) ? 1 : 0;
            // 处理图片URL
            $item['image'] = $item['image'] ? \app\common\service\FileService::getFileUrl($item['image']) : '';
            $item['logo'] = $item['logo'] ? \app\common\service\FileService::getFileUrl($item['logo']) : '';
        }

        return $lists;
    }

    /**
     * @notes 获取数量
     * @return int
     */
    public function count(): int
    {
        $where = [
            ['m.status', '=', 1]
        ];

        if (isset($this->params['keyword']) && $this->params['keyword']) {
            $where[] = ['m.name|m.desc', 'like', '%' . $this->params['keyword'] . '%'];
        }

        return Merchant::alias('m')
            ->where($where)
            ->count();
    }
}