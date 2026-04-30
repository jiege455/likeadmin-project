<?php
namespace app\adminapi\lists\article;

use app\adminapi\lists\BaseAdminDataLists;
use app\common\lists\ListsSearchInterface;
use app\common\model\article\Article;

/**
 * 商户文章列表
 */
class MerchantArticleLists extends BaseAdminDataLists implements ListsSearchInterface
{
    public function setSearch(): array
    {
        return [
            '%like%' => ['title', 'author'],
            '=' => ['cid', 'audit_status']
        ];
    }

    public function lists(): array
    {
        return Article::with(['merchant'])
            ->where('merchant_id', '>', 0)
            ->where($this->searchWhere)
            ->limit($this->limitOffset, $this->limitLength)
            ->order('id', 'desc')
            ->select()
            ->toArray();
    }

    public function count(): int
    {
        return Article::where('merchant_id', '>', 0)
            ->where($this->searchWhere)
            ->count();
    }
}
