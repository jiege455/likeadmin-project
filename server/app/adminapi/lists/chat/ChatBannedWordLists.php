<?php
/**
 * 开发者公众号：杰哥网络科技
 * QQ: 2711793818 杰哥
 * 违禁词列表
 */
namespace app\adminapi\lists\chat;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\model\chat\ChatBannedWord;
use app\common\lists\ListsSearchInterface;

class ChatBannedWordLists extends BaseAdminDataLists implements ListsSearchInterface
{
    public function setSearch(): array
    {
        return [
            '%like%' => ['word'],
            '=' => ['type', 'status'],
        ];
    }

    public function lists(): array
    {
        $lists = ChatBannedWord::where($this->searchWhere)
            ->field('id, word, type, replace_word, status, create_time')
            ->order(['id' => 'desc'])
            ->limit($this->limitOffset, $this->limitLength)
            ->select()
            ->toArray();
        
        foreach ($lists as &$item) {
            $item['type_text'] = $item['type'] == 1 ? '违禁词' : '敏感词';
        }
        
        return $lists;
    }

    public function count(): int
    {
        return ChatBannedWord::where($this->searchWhere)->count();
    }
}
