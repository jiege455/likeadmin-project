<?php
/**
 * 文章标签控制器
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\api\controller;

use app\api\controller\BaseApiController;
use app\common\model\article\ArticleTag;
use app\common\model\article\ArticleTagRelation;
use app\common\service\FileService;
use think\facade\Db;

class ArticleTagController extends BaseApiController
{
    public array $notNeedLogin = ['lists', 'hot', 'articles'];

    public function lists()
    {
        $pageNo = $this->request->get('page_no', 1);
        $pageSize = $this->request->get('page_size', 20);

        $lists = ArticleTag::where('is_show', 1)
            ->where('delete_time', null)
            ->order('article_count', 'desc')
            ->order('sort', 'desc')
            ->order('id', 'desc')
            ->page($pageNo, $pageSize)
            ->field('id,name,article_count')
            ->select()
            ->toArray();

        $count = ArticleTag::where('is_show', 1)
            ->where('delete_time', null)
            ->count();

        return $this->data([
            'lists' => $lists,
            'count' => $count
        ]);
    }

    public function hot()
    {
        $limit = $this->request->get('limit', 10);

        $lists = ArticleTag::where('is_show', 1)
            ->where('is_hot', 1)
            ->where('delete_time', null)
            ->order('article_count', 'desc')
            ->order('sort', 'desc')
            ->limit($limit)
            ->field('id,name')
            ->select()
            ->toArray();

        return $this->data($lists);
    }

    public function all()
    {
        // 如果用户已登录，只返回用户自己创建的标签
        if ($this->userId) {
            $lists = ArticleTag::where('user_id', $this->userId)
                ->where('delete_time', null)
                ->order('create_time', 'desc')
                ->field('id,name')
                ->select()
                ->toArray();
        } else {
            $lists = [];
        }

        return $this->data($lists);
    }

    public function create()
    {
        if (!$this->userId) {
            return $this->fail('请先登录');
        }

        $name = trim($this->request->post('name', ''));
        $name = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $name = preg_replace('/[^\p{L}\p{N}\s\-_]/u', '', $name);
        $name = trim($name);
        
        if (empty($name)) {
            return $this->fail('标签名称不能为空');
        }

        if (mb_strlen($name) < 2 || mb_strlen($name) > 10) {
            return $this->fail('标签名称需要2-10个字符');
        }

        // 检查当前用户是否已创建同名标签
        $existTag = Db::name('article_tag')
            ->where('name', $name)
            ->where('user_id', $this->userId)
            ->where('delete_time', null)
            ->find();
        
        if ($existTag) {
            return $this->data([
                'id' => $existTag['id'],
                'name' => $existTag['name']
            ]);
        }

        $data = [
            'name' => $name,
            'user_id' => $this->userId,
            'is_show' => 1,
            'is_hot' => 0,
            'sort' => 0,
            'click_count' => 0,
            'article_count' => 0,
            'create_time' => time(),
            'update_time' => time()
        ];
        
        $id = Db::name('article_tag')->insertGetId($data);

        return $this->data([
            'id' => $id,
            'name' => $name
        ]);
    }

    public function articles()
    {
        $tagId = $this->request->get('tag_id');
        if (empty($tagId)) {
            return $this->fail('标签ID不能为空');
        }

        $pageNo = $this->request->get('page_no', 1);
        $pageSize = $this->request->get('page_size', 10);

        $articleIds = ArticleTagRelation::getArticleIdsByTagId($tagId);
        if (empty($articleIds)) {
            return $this->data([
                'lists' => [],
                'count' => 0
            ]);
        }

        $lists = Db::name('article')
            ->whereIn('id', $articleIds)
            ->where('is_show', 1)
            ->where('delete_time', null)
            ->order('create_time', 'desc')
            ->page($pageNo, $pageSize)
            ->field('id,title,image,desc,click_virtual,click_actual,create_time,merchant_id')
            ->select()
            ->toArray();

        $count = Db::name('article')
            ->whereIn('id', $articleIds)
            ->where('is_show', 1)
            ->where('delete_time', null)
            ->count();

        foreach ($lists as &$item) {
            $item['click'] = $item['click_virtual'] + $item['click_actual'];
            $item['create_time'] = date('Y-m-d H:i', $item['create_time']);
            if (!empty($item['image'])) {
                $item['image'] = FileService::getFileUrl($item['image']);
            }
        }

        return $this->data([
            'lists' => $lists,
            'count' => $count
        ]);
    }

    public function delete()
    {
        if (!$this->userId) {
            return $this->fail('请先登录');
        }

        $tagId = $this->request->post('id');
        if (empty($tagId)) {
            return $this->fail('标签ID不能为空');
        }

        $tag = ArticleTag::find($tagId);
        if (!$tag) {
            return $this->fail('标签不存在');
        }

        // 只能删除自己创建的标签
        if ($tag['user_id'] != $this->userId) {
            return $this->fail('无权删除此标签');
        }

        // 删除标签关联关系
        ArticleTagRelation::where('tag_id', $tagId)->delete();

        // 软删除标签
        $tag->delete_time = time();
        $tag->save();

        return $this->success('删除成功');
    }
}
