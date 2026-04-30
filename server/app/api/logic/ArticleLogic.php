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

namespace app\api\logic;

use app\common\enum\YesNoEnum;
use app\common\logic\BaseLogic;
use app\common\model\article\Article;
use app\common\model\article\ArticleCate;
use app\common\model\article\ArticleCollect;
use think\facade\Db;
use app\common\logic\AccountLogLogic;
use app\common\enum\user\AccountLogEnum;
use app\common\logic\DistributionLogic;
use app\common\model\user\User;

use app\common\model\article\ArticleOrder;
use app\common\service\ConfigService;

/**
 * 文章逻辑
 * Class ArticleLogic
 * @package app\api\logic
 */
class ArticleLogic extends BaseLogic
{

    /**
     * @notes 文章详情
     * @param $articleId
     * @param $userId
     * @return array
     * @author 段誉
     * @date 2022/9/20 17:09
     */
    public static function detail($articleId, $userId)
    {
        $article = Article::getArticleDetailArr($articleId);
        $article['collect'] = ArticleCollect::isCollectArticle($userId, $articleId);

        $article['is_buy'] = false;
        $article['need_pay'] = false;
        $article['is_paid'] = true;

        $article['is_public'] = true;
        $article['merchant_name'] = '';
        $article['merchant_image'] = '';
        $article['tag_list'] = [];

        // 获取文章标签
        $article['tag_list'] = \app\common\model\article\ArticleTag::alias('t')
            ->join('la_article_tag_relation r', 't.id = r.tag_id')
            ->where('r.article_id', $articleId)
            ->field('t.id, t.name')
            ->select()
            ->toArray();

        $article['can_distribute'] = false;
        $article['commission_amount'] = '0.00';

        // 【性能优化】只查询一次商户信息，避免N+1查询问题
        $merchantInfo = null;
        if (isset($article['merchant_id']) && $article['merchant_id'] > 0) {
            $merchantInfo = \app\common\model\merchant\Merchant::where('id', $article['merchant_id'])->find();
            if ($merchantInfo) {
                $article['merchant_name'] = $merchantInfo['name'] ?? '';
                $logo = $merchantInfo['logo'] ?? '';
                if ($logo && !str_starts_with($logo, 'http')) {
                    $logo = \app\common\service\FileService::getFileUrl($logo);
                }
                $article['merchant_image'] = $logo;
            }
        }

        // 分销判断（移到付费判断外面，免费文章也能推广）
        $merchantDistributionSwitch = true;
        if ($merchantInfo && isset($merchantInfo['distribution_switch']) && $merchantInfo['distribution_switch'] == 0) {
            $merchantDistributionSwitch = false;
        }
        if ($merchantDistributionSwitch && isset($article['distribution_switch']) && $article['distribution_switch'] == 1) {
            $article['can_distribute'] = true;
            if (isset($article['price']) && $article['price'] > 0 && isset($article['commission_ratio']) && $article['commission_ratio'] > 0) {
                $realRatio = bcdiv($article['commission_ratio'], 100, 4);
                $article['commission_amount'] = bcmul($article['price'], $realRatio, 2);
            }
        }

        if (isset($article['price']) && $article['price'] > 0) {
            $article['need_pay'] = true;
            $article['is_paid'] = false;

            $isMerchant = false;
            if ($userId > 0 && isset($article['merchant_id']) && $article['merchant_id'] > 0) {
                 $merchant = \app\common\model\merchant\Merchant::where('user_id', $userId)->find();
                 if ($merchant && $merchant['id'] == $article['merchant_id']) {
                     $isMerchant = true;
                 }
            }

            $currentIssueNo = $article['issue_no'] ?? '';
            $isBuy = false;
            
            // 只有用户已登录时才查询订单
            if ($userId > 0) {
                $buyWhere = [
                    'user_id' => $userId,
                    'article_id' => $articleId,
                    'pay_status' => 1
                ];
                if ($currentIssueNo !== '') {
                    $buyWhere['issue_no'] = $currentIssueNo;
                }
                $isBuy = ArticleOrder::where($buyWhere)->find() ? true : false;
            }

            if (!$isBuy && !$isMerchant) {
                unset($article['hidden_content']);
            } else {
                $article['is_buy'] = true;
                $article['is_paid'] = true;
            }
        }

        $article['watermark'] = [
            'enable' => ConfigService::get('article_watermark', 'enable', 0),
            'text' => ConfigService::get('article_watermark', 'text', '杰哥网络科技'),
            'contact' => ConfigService::get('article_watermark', 'contact', 'QQ:2711793818'),
            'position' => ConfigService::get('article_watermark', 'position', 'right_bottom'),
            'opacity' => ConfigService::get('article_watermark', 'opacity', 0.15),
        ];

        return $article;
    }


    /**
     * @notes 加入收藏
     * @param $userId
     * @param $articleId
     * @author 段誉
     * @date 2022/9/20 16:52
     */
    public static function addCollect($articleId, $userId)
    {
        $where = ['user_id' => $userId, 'article_id' => $articleId];
        $collect = ArticleCollect::where($where)->findOrEmpty();
        if ($collect->isEmpty()) {
            ArticleCollect::create([
                'user_id' => $userId,
                'article_id' => $articleId,
                'status' => YesNoEnum::YES
            ]);
        } else {
            ArticleCollect::update([
                'id' => $collect['id'],
                'status' => YesNoEnum::YES
            ]);
        }
    }


    /**
     * @notes 取消收藏
     * @param $articleId
     * @param $userId
     * @author 段誉
     * @date 2022/9/20 16:59
     */
    public static function cancelCollect($articleId, $userId)
    {
        ArticleCollect::update(['status' => YesNoEnum::NO], [
            'user_id' => $userId,
            'article_id' => $articleId,
            'status' => YesNoEnum::YES
        ]);
    }


    /**
     * @notes 文章分类
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     * @author 段誉
     * @date 2022/9/23 14:11
     */
    public static function cate()
    {
        return ArticleCate::field('id,name,is_series')
            ->where('is_show', '=', 1)
            ->order(['sort' => 'desc', 'id' => 'desc'])
            ->select()->toArray();
    }

    /**
     * @notes 购买文章
     * @param $articleId
     * @param $userId
     * @return array
     * @throws \Exception
     */
    public static function buy($articleId, $userId)
    {
        $article = Article::findOrEmpty($articleId);
        if ($article->isEmpty()) {
            throw new \Exception('文章不存在');
        }
        if ($article->price <= 0) {
            throw new \Exception('该文章无需购买');
        }

        $currentIssueNo = $article->issue_no ?? '';
        $buyWhere = [
            'user_id' => $userId,
            'article_id' => $articleId,
            'pay_status' => 1
        ];
        if ($currentIssueNo !== '') {
            $buyWhere['issue_no'] = $currentIssueNo;
        }
        $isBuy = ArticleOrder::where($buyWhere)->find();
        if ($isBuy) {
            throw new \Exception('您已购买过该文章当前期次');
        }

        $unpaidWhere = [
            'user_id' => $userId,
            'article_id' => $articleId,
            'pay_status' => 0
        ];
        if ($currentIssueNo !== '') {
            $unpaidWhere['issue_no'] = $currentIssueNo;
        }
        $existOrder = ArticleOrder::where($unpaidWhere)->find();
        if ($existOrder) {
            return ['order_id' => $existOrder['id']];
        }

        $orderSn = generate_sn(ArticleOrder::class, 'order_sn');
        $orderId = ArticleOrder::insertGetId([
            'order_sn' => $orderSn,
            'user_id' => $userId,
            'article_id' => $articleId,
            'issue_no' => $article->issue_no ?? '',
            'merchant_id' => $article->merchant_id ?? 0,
            'order_amount' => $article->price,
            'distribution_ratio' => $article->commission_ratio ?? 0,
            'pay_status' => 0,
            'create_time' => time(),
            'update_time' => time()
        ]);

        return ['order_id' => $orderId];
    }

}
