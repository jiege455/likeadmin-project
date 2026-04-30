<?php
/**
 * 文章标签关联模型
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\common\model\article;

use app\common\model\BaseModel;

class ArticleTagRelation extends BaseModel
{
    protected $name = 'article_tag_relation';

    public static function saveTags($articleId, $tagIds)
    {
        self::where('article_id', $articleId)->delete();
        
        if (empty($tagIds)) {
            return;
        }

        $tagIds = is_array($tagIds) ? $tagIds : explode(',', $tagIds);
        $tagIds = array_filter(array_unique($tagIds));

        foreach ($tagIds as $tagId) {
            $relation = new self();
            $relation->article_id = $articleId;
            $relation->tag_id = intval($tagId);
            $relation->create_time = time();
            $relation->save();
        }
    }

    public static function deleteByArticle($articleId)
    {
        self::where('article_id', $articleId)->delete();
    }

    public static function getArticleIdsByTagId($tagId)
    {
        return self::where('tag_id', $tagId)->column('article_id');
    }
}
