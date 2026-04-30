<?php
namespace app\adminapi\logic\article;

use app\common\logic\BaseLogic;
use app\common\model\article\Article;

/**
 * 商户文章逻辑
 */
class MerchantArticleLogic extends BaseLogic
{
    /**
     * @notes 审核文章
     */
    public static function audit($params)
    {
        try {
            $article = Article::find($params['id']);
            if (!$article) {
                throw new \Exception('文章不存在');
            }
            
            $article->audit_status = $params['audit_status'];
            $article->update_time = time();
            $article->save();

            return true;
        } catch (\Exception $e) {
            self::setError($e->getMessage());
            return false;
        }
    }

    /**
     * @notes 删除文章
     */
    public static function delete($params)
    {
        return Article::destroy($params['id']);
    }

    /**
     * @notes 详情
     */
    public static function detail($params)
    {
        return Article::with(['merchant'])->findOrEmpty($params['id'])->toArray();
    }
}
