<?php
// +----------------------------------------------------------------------
// | likeadmin快速开发前后端分离管理后台（PHP版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权logo
// | gitee下载：https://gitee.com/likeshop_gitee/likeadmin
// | github下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------

namespace app\api\lists\article;

use app\api\lists\BaseApiDataLists;
use app\common\enum\YesNoEnum;
use app\common\lists\ListsSearchInterface;
use app\common\model\article\Article;
use app\common\model\article\ArticleCollect;
use app\common\model\article\ArticleOrder;
use app\common\model\article\ArticleTag;
use app\common\model\article\ArticleTagRelation;


/**
 * 文章列表
 * Class ArticleLists
 * @package app\api\lists\article
 */
class ArticleLists extends BaseApiDataLists implements ListsSearchInterface
{

    /**
     * @notes 搜索条件
     * @return \string[][]
     * @author 段誉
     * @date 2022/9/16 18:54
     */
    public function setSearch(): array
    {
        return [
            '=' => ['cid', 'merchant_id']
        ];
    }


    /**
     * @notes 自定查询条件
     * @return array
     * @author 段誉
     * @date 2022/10/25 16:53
     */
    public function queryWhere()
    {
        $where[] = ['is_show', '=', 1];
        $where[] = ['audit_status', '=', 1];
        // 系列文章或非系列但已发布的文章
        $where[] = function ($query) {
            $query->where('series_id', '>', 0);
            $query->whereOr(function ($query) {
                $query->where('series_id', '=', 0);
                $query->where('issue_status', '>', 0);
            });
        };

        if (!empty($this->params['keyword'])) {
            $where[] = ['title', 'like', '%' . $this->params['keyword'] . '%'];
        }

        // 支持商户文章筛选
        if (isset($this->params['is_merchant']) && $this->params['is_merchant'] == 1) {
            $where[] = ['merchant_id', '>', 0];
        }

        // 价格类型筛选：free=免费，paid=付费
        if (!empty($this->params['price_type'])) {
            if ($this->params['price_type'] == 'free') {
                $where[] = ['price', '=', 0];
            } elseif ($this->params['price_type'] == 'paid') {
                $where[] = ['price', '>', 0];
            }
        }

        // 价格区间筛选
        if (!empty($this->params['price_range'])) {
            $priceRange = $this->params['price_range'];
            if ($priceRange == '0-10') {
                $where[] = ['price', '>=', 0];
                $where[] = ['price', '<=', 10];
            } elseif ($priceRange == '10-50') {
                $where[] = ['price', '>=', 10];
                $where[] = ['price', '<=', 50];
            } elseif ($priceRange == '50-') {
                $where[] = ['price', '>=', 50];
            }
        }

        return $where;
    }


    /**
     * @notes 获取文章列表
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2022/9/16 18:55
     */
    public function lists(): array
    {
        $orderRaw = 'sort desc, id desc';
        $sortType = $this->params['sort'] ?? 'default';
        // 最新排序
        if ($sortType == 'new') {
            $orderRaw = 'id desc';
        }
        // 最热排序
        if ($sortType == 'hot') {
            $orderRaw = 'click_actual + click_virtual desc, id desc';
        }

        $field = 'id,cid,title,desc,image,click_virtual,click_actual,create_time,price,issue_no,merchant_id,series_id,issue_status';
        $result = Article::field($field)
            ->where($this->queryWhere())
            ->where($this->searchWhere)
            ->orderRaw($orderRaw)
            ->limit($this->limitOffset, $this->limitLength)
            ->select()->toArray();

        $articleIds = array_column($result, 'id');

        $collectIds = ArticleCollect::where(['user_id' => $this->userId, 'status' => YesNoEnum::YES])
            ->whereIn('article_id', $articleIds)
            ->column('article_id');

        $paidOrders = ArticleOrder::where([
            'user_id' => $this->userId,
            'pay_status' => 1
        ])->whereIn('article_id', $articleIds)->column('article_id,issue_no', 'id');

        // 获取商户信息
        $merchantIds = array_filter(array_column($result, 'merchant_id'));
        $merchants = [];
        if (!empty($merchantIds)) {
            $merchantList = \app\common\model\merchant\Merchant::whereIn('id', $merchantIds)
                ->field('id,name,logo')
                ->select()
                ->toArray();
            foreach ($merchantList as $m) {
                $merchants[$m['id']] = $m;
            }
        }

        // 获取文章标签
        $articleTags = [];
        if (!empty($articleIds)) {
            $tagRelations = ArticleTagRelation::whereIn('article_id', $articleIds)->select()->toArray();
            $tagIds = array_unique(array_column($tagRelations, 'tag_id'));
            if (!empty($tagIds)) {
                $tags = ArticleTag::whereIn('id', $tagIds)->where('is_show', 1)->column('id,name', 'id');
                foreach ($tagRelations as $rel) {
                    if (isset($tags[$rel['tag_id']])) {
                        $articleTags[$rel['article_id']][] = $tags[$rel['tag_id']];
                    }
                }
            }
        }

        foreach ($result as &$item) {
            $item['collect'] = in_array($item['id'], $collectIds);
            $item['is_buy'] = false;
            $currentIssueNo = $item['issue_no'] ?? '';
            foreach ($paidOrders as $order) {
                if ($order['article_id'] == $item['id']) {
                    if ($currentIssueNo === '' || $order['issue_no'] === $currentIssueNo) {
                        $item['is_buy'] = true;
                        break;
                    }
                }
            }
            
            // 添加商户信息
            if (!empty($item['merchant_id']) && isset($merchants[$item['merchant_id']])) {
                $merchant = $merchants[$item['merchant_id']];
                $item['merchant_name'] = $merchant['name'];
                $item['merchant_image'] = !empty($merchant['logo']) 
                    ? \app\common\service\FileService::getFileUrl($merchant['logo']) 
                    : '';
            } else {
                $item['merchant_name'] = '';
                $item['merchant_image'] = '';
            }

            // 添加标签信息
            $item['tag_list'] = isset($articleTags[$item['id']]) ? $articleTags[$item['id']] : [];
        }

        return $result;
    }


    /**
     * @notes 获取文章数量
     * @return int
     * @author 段誉
     * @date 2022/9/16 18:55
     */
    public function count(): int
    {
        return Article::where($this->searchWhere)
            ->where($this->queryWhere())
            ->count();
    }
}