<?php
namespace app\adminapi\lists\merchant;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\model\merchant\MerchantComplaint;
use app\common\lists\ListsSearchTrait;
use think\facade\Log;

class ComplaintLists extends BaseAdminDataLists
{
    use ListsSearchTrait;

    public function setSearch(): array
    {
        return [
            '=' => ['user_id', 'merchant_id', 'status', 'type']
        ];
    }

    public function lists(): array
    {
        try {
            $lists = MerchantComplaint::with(['user', 'merchant'])
                ->where($this->searchWhere)
                ->order('id', 'desc')
                ->limit($this->limitOffset, $this->limitLength)
                ->select()
                ->toArray();

            foreach ($lists as &$item) {
                $item['user_nickname'] = !empty($item['user']) ? $item['user']['nickname'] : '未知用户';
                $item['user_avatar'] = !empty($item['user']) ? $item['user']['avatar'] : '';
                $item['merchant_name'] = !empty($item['merchant']) ? $item['merchant']['name'] : '未知商户';
                $item['type_text'] = MerchantComplaint::getTypeDesc($item['type'] ?? 1);
                if (is_numeric($item['create_time'])) {
                    $item['create_time'] = date('Y-m-d H:i:s', $item['create_time']);
                }
                $item['images'] = empty($item['images']) ? [] : json_decode($item['images'], true);
            }

            return $lists;
        } catch (\Throwable $e) {
            Log::error('投诉列表查询错误: ' . $e->getMessage() . ' 文件: ' . $e->getFile() . ' 行: ' . $e->getLine());
            throw $e;
        }
    }

    public function count(): int
    {
        return MerchantComplaint::where($this->searchWhere)->count();
    }
}
