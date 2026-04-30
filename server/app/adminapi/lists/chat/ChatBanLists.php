<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 禁言记录列表
 */
namespace app\adminapi\lists\chat;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\model\chat\ChatBan;
use app\common\lists\ListsSearchInterface;
use app\common\model\user\User;
use app\common\model\merchant\Merchant;

class ChatBanLists extends BaseAdminDataLists implements ListsSearchInterface
{
    public function setSearch(): array
    {
        return [
            '=' => ['user_id', 'user_type', 'ban_type', 'status'],
        ];
    }

    public function lists(): array
    {
        $lists = ChatBan::where($this->searchWhere)
            ->field('id, user_id, user_type, ban_type, reason, admin_id, expire_time, status, create_time')
            ->order(['id' => 'desc'])
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();
        
        foreach ($lists as &$item) {
            $item['user_type_text'] = ChatBan::getUserTypeText($item['user_type']);
            $item['ban_type_text'] = ChatBan::getBanTypeText($item['ban_type']);
            $item['status_text'] = $item['status'] == 1 ? '禁言中' : '已解除';
            
            if ($item['user_type'] == ChatBan::USER_TYPE_USER) {
                $user = User::find($item['user_id']);
                $item['user_info'] = $user ? [
                    'id' => $user->id,
                    'nickname' => $user->nickname,
                    'avatar' => $user->avatar
                ] : null;
            } else {
                $merchant = Merchant::find($item['user_id']);
                $item['user_info'] = $merchant ? [
                    'id' => $merchant->id,
                    'name' => $merchant->name,
                    'logo' => $merchant->logo
                ] : null;
            }

            if ($item['expire_time']) {
                $expireTimestamp = is_numeric($item['expire_time']) ? $item['expire_time'] : strtotime($item['expire_time']);
                if ($expireTimestamp < time() && $item['status'] == 1) {
                    $item['status_text'] = '已过期';
                }
            }
        }
        
        return $lists;
    }

    public function count(): int
    {
        return ChatBan::where($this->searchWhere)->count();
    }
}
