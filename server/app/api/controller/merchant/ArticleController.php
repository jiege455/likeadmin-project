<?php
/**
 * 商家文章控制器
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\api\controller\merchant;

use app\api\controller\BaseApiController;
use app\common\service\FileService;
use app\common\model\article\ArticleTag;
use app\common\model\article\ArticleTagRelation;
use think\facade\Db;

class ArticleController extends BaseApiController
{
    public function lists()
    {
        $userId = $this->userId;
        $merchants = Db::name('merchant')->where('user_id', $userId)->select()->toArray();

        if (empty($merchants)) {
            return $this->fail('您还不是商户');
        }

        $merchantIds = array_column($merchants, 'id');
        $where = [
            ['a.merchant_id', 'in', $merchantIds],
            ['a.delete_time', '=', null]
        ];

        $seriesId = $this->request->get('series_id');
        if ($seriesId !== null && $seriesId !== '') {
            if ($seriesId == 1) {
                $where[] = ['a.series_id', '>', 0];
            } elseif ($seriesId == 0) {
                $where[] = ['a.series_id', '=', 0];
            }
        }

        $lists = Db::name('article')
            ->alias('a')
            ->leftJoin('article_cate c', 'a.cid = c.id')
            ->leftJoin('article_cate s', 'a.series_id = s.id')
            ->field('a.*, c.name as cate_name, s.name as series_name')
            ->where($where)
            ->order('a.create_time', 'desc')
            ->page($this->request->get('page_no', 1), $this->request->get('page_size', 10))
            ->select()
            ->toArray();
            
        $count = Db::name('article')
            ->alias('a')
            ->where($where)
            ->count();

        foreach ($lists as &$item) {
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

    public function detail()
    {
        $id = $this->request->get('id');
        $userId = $this->userId;
        $merchants = Db::name('merchant')->where('user_id', $userId)->select()->toArray();

        if (empty($merchants)) {
            return $this->fail('您还不是商户');
        }

        $merchantIds = array_column($merchants, 'id');

        $article = Db::name('article')
            ->where('id', $id)
            ->where('merchant_id', 'in', $merchantIds)
            ->where('delete_time', null)
            ->find();

        if (!$article) {
            return $this->fail('文章不存在');
        }

        if (!empty($article['image'])) {
            $article['image'] = FileService::getFileUrl($article['image']);
        }

        $article['tag_list'] = ArticleTag::getTagsByArticleId($id);

        return $this->data($article);
    }

    public function save()
    {
        $post = $this->request->post();
        $userId = $this->userId;
        $merchants = Db::name('merchant')->where('user_id', $userId)->select()->toArray();

        if (empty($merchants)) {
            return $this->fail('您还不是商户');
        }

        $merchantId = $merchants[0]['id'];

        if (empty($post['title'])) {
            return $this->fail('标题不能为空');
        }

        $seriesId = intval($post['series_id'] ?? 0);
        $issueNo = $post['issue_no'] ?? '';

        // 系列文章验证（通过 series_id > 0 判断）
        if ($seriesId > 0) {
            if (empty($issueNo)) {
                return $this->fail('请输入期次号');
            }
            if (empty($post['hidden_content'])) {
                return $this->fail('请输入当前期内容');
            }

            $series = Db::name('article_cate')
                ->where('id', $seriesId)
                ->where('is_series', 1)
                ->where('delete_time', null)
                ->find();

            if (!$series) {
                return $this->fail('所选系列不存在');
            }
        } else {
            // 普通文章必须有内容（付费文章在hidden_content，免费文章在content）
            $price = floatval($post['price'] ?? 0);
            if ($price > 0) {
                // 付费文章：hidden_content必填
                if (empty($post['hidden_content'])) {
                    return $this->fail('付费文章内容不能为空');
                }
            } else {
                // 免费文章：content必填
                if (empty($post['content'])) {
                    return $this->fail('内容不能为空');
                }
            }
        }

        $image = $post['image'] ?? '';
        if (!empty($image)) {
            $baseUrl = request()->domain();
            if (strpos($image, $baseUrl) === 0) {
                $image = str_replace($baseUrl, '', $image);
            }
        }

        $data = [
            'title' => $post['title'],
            'cid' => $post['cid'] ?? 1,
            'desc' => $post['desc'] ?? '',
            'content' => $post['content'] ?? '',
            'hidden_content' => $post['hidden_content'] ?? '',
            'prev_issue_content' => $post['prev_issue_content'] ?? '',
            'prev_issue_no' => $post['prev_issue_no'] ?? '',
            'image' => $image,
            'price' => floatval($post['price'] ?? 0),
            'commission_ratio' => floatval($post['commission_ratio'] ?? 0),
            'merchant_id' => $merchantId,
            'is_show' => intval($post['is_show'] ?? 1),
            'audit_status' => 1,
            'issue_status' => 1,
            'series_id' => $seriesId,
            'issue_no' => $issueNo,
            'update_time' => time()
        ];

        // 自动生成简介
        if (empty($data['desc'])) {
            if ($seriesId > 0 && !empty($data['hidden_content'])) {
                $data['desc'] = mb_substr(strip_tags($data['hidden_content']), 0, 200);
            } elseif (!empty($data['content'])) {
                $data['desc'] = mb_substr(strip_tags($data['content']), 0, 200);
            }
        }

        if (empty($post['id'])) {
            $data['create_time'] = time();
            $articleId = Db::name('article')->insertGetId($data);
        } else {
            Db::name('article')->where('id', $post['id'])->where('merchant_id', $merchantId)->update($data);
            $articleId = $post['id'];
        }

        if ($seriesId > 0) {
            $publishedCount = Db::name('article')
                ->where('series_id', $seriesId)
                ->where('delete_time', null)
                ->count();
            
            Db::name('article_cate')
                ->where('id', $seriesId)
                ->update(['published_issues' => $publishedCount]);
        }

        $tagIds = $post['tag_ids'] ?? '';
        if (!empty($tagIds)) {
            $tagIdArr = is_array($tagIds) ? $tagIds : explode(',', $tagIds);
            $tagIdArr = array_filter(array_map('intval', $tagIdArr));
            $tagIdArr = array_slice($tagIdArr, 0, 5);
            
            ArticleTagRelation::saveTags($articleId, $tagIdArr);
            
            $tagNames = [];
            foreach ($tagIdArr as $tid) {
                $tag = ArticleTag::find($tid);
                if ($tag) {
                    $tagNames[] = $tag['name'];
                }
            }
            Db::name('article')->where('id', $articleId)->update(['tags' => implode(',', $tagNames)]);
        } else {
            ArticleTagRelation::deleteByArticle($articleId);
            Db::name('article')->where('id', $articleId)->update(['tags' => '']);
        }

        if (empty($post['id']) && $data['is_show'] == 1) {
            \app\common\logic\PushNotifyLogic::onArticlePublish(
                $articleId,
                $merchantId,
                $data['title'],
                $data['content'] . ' ' . ($data['hidden_content'] ?? '')
            );
        }

        return $this->success('保存成功');
    }

    public function delete()
    {
        $id = $this->request->post('id');
        $userId = $this->userId;
        $merchants = Db::name('merchant')->where('user_id', $userId)->select()->toArray();

        if (empty($merchants)) return $this->fail('无权操作');

        $merchantIds = array_column($merchants, 'id');

        $article = Db::name('article')
            ->where('id', $id)
            ->where('merchant_id', 'in', $merchantIds)
            ->find();

        if (!$article) {
            return $this->fail('文章不存在');
        }

        Db::name('article')
            ->where('id', $id)
            ->update(['delete_time' => time()]);

        ArticleTagRelation::deleteByArticle($id);

        if ($article['series_id'] > 0) {
            $publishedCount = Db::name('article')
                ->where('series_id', $article['series_id'])
                ->where('delete_time', null)
                ->count();
            
            Db::name('article_cate')
                ->where('id', $article['series_id'])
                ->update(['published_issues' => $publishedCount]);
        }

        return $this->success('删除成功');
    }
}
