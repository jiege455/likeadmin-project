<?php
/**
 * 文章标签模型
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\common\model\article;

use app\common\model\BaseModel;
use think\model\concern\SoftDelete;

class ArticleTag extends BaseModel
{
    use SoftDelete;

    protected $name = 'article_tag';
    protected $deleteTime = 'delete_time';

    public function getIsShowDescAttr($value, $data)
    {
        return $data['is_show'] ? '显示' : '隐藏';
    }

    public function getIsHotDescAttr($value, $data)
    {
        return $data['is_hot'] ? '是' : '否';
    }

    public function getArticleCountAttr($value, $data)
    {
        return ArticleTagRelation::where('tag_id', $data['id'])->count();
    }

    public static function getTagByName($name)
    {
        return self::where('name', $name)->where('delete_time', null)->find();
    }

    public static function createTag($name)
    {
        $data = [
            'name' => $name,
            'is_show' => 1,
            'is_hot' => 0,
            'sort' => 0,
            'click_count' => 0,
            'article_count' => 0,
            'create_time' => time(),
            'update_time' => time()
        ];
        $id = \think\facade\Db::name('article_tag')->insertGetId($data);
        return self::find($id);
    }

    public static function getTagsByArticleId($articleId)
    {
        $tagIds = ArticleTagRelation::where('article_id', $articleId)->column('tag_id');
        if (empty($tagIds)) {
            return [];
        }
        return self::whereIn('id', $tagIds)->where('is_show', 1)->where('delete_time', null)->field('id,name')->select()->toArray();
    }
}
