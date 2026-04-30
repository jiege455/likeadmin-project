<?php
namespace app\common\logic;

use app\common\model\user\UserPushKeyword;
use app\common\model\notice\PushMessage;
use app\common\model\merchant\MerchantFollow;
use think\facade\Log;
use think\facade\Db;

/**
 * 推送通知逻辑
 * 开发者：杰哥网络科技 qq2711793818 杰哥
 */
class PushNotifyLogic
{
    public static function onArticlePublish($articleId, $merchantId, $title, $content = '')
    {
        try {
            $keywords = UserPushKeyword::where('merchant_id', $merchantId)
                ->where('is_enable', 1)
                ->where('delete_time', null)
                ->field('user_id, keyword')
                ->select()
                ->toArray();

            if (empty($keywords)) {
                return;
            }

            $userKeywords = [];
            foreach ($keywords as $item) {
                $userId = $item['user_id'];
                $keyword = $item['keyword'];
                
                if (!isset($userKeywords[$userId])) {
                    $userKeywords[$userId] = [];
                }
                $userKeywords[$userId][] = $keyword;
            }

            $searchText = $title . ' ' . strip_tags($content);

            foreach ($userKeywords as $userId => $keywordList) {
                $follow = MerchantFollow::where([
                    'user_id' => $userId,
                    'merchant_id' => $merchantId
                ])->find();

                if (!$follow || ($follow->push_enable ?? 1) == 0) {
                    continue;
                }

                $matchedKeyword = null;
                foreach ($keywordList as $keyword) {
                    if (stripos($searchText, $keyword) !== false) {
                        $matchedKeyword = $keyword;
                        break;
                    }
                }

                if ($matchedKeyword === null) {
                    continue;
                }

                if (PushMessage::hasPushed($userId, $articleId)) {
                    continue;
                }

                $merchant = Db::name('merchant')->where('id', $merchantId)->find();
                $merchantName = $merchant ? $merchant['name'] : '商家';

                PushMessage::create([
                    'user_id' => $userId,
                    'merchant_id' => $merchantId,
                    'article_id' => $articleId,
                    'keyword' => $matchedKeyword,
                    'title' => $merchantName . '发布了新文章',
                    'content' => '您关注的关键词"' . $matchedKeyword . '"匹配到文章：' . $title,
                    'is_read' => 0,
                    'push_type' => 1,
                    'create_time' => time()
                ]);

                self::sendWebSocketPush($userId, [
                    'type' => 'article_push',
                    'merchant_id' => $merchantId,
                    'article_id' => $articleId,
                    'keyword' => $matchedKeyword,
                    'title' => $title
                ]);
            }

        } catch (\Exception $e) {
            Log::write('推送通知失败: ' . $e->getMessage(), 'error');
        }
    }

    protected static function sendWebSocketPush($userId, $data)
    {
        try {
            $fds = Db::name('websocket_user')
                ->where('user_id', $userId)
                ->column('fd');

            if (empty($fds)) {
                return;
            }

            $server = app('swoole.server');
            if (!$server) {
                return;
            }

            $message = json_encode([
                'type' => 'push_article',
                'data' => $data
            ]);

            foreach ($fds as $fd) {
                if ($server->isEstablished($fd)) {
                    $server->push($fd, $message);
                }
            }
        } catch (\Exception $e) {
            Log::write('WebSocket推送失败: ' . $e->getMessage(), 'error');
        }
    }
}
