<?php
/**
 * 文章水印配置逻辑
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\adminapi\logic\setting;

use app\common\logic\BaseLogic;
use app\common\service\ConfigService;

class ArticleWatermarkLogic extends BaseLogic
{
    public static function getConfig()
    {
        return [
            'enable' => ConfigService::get('article_watermark', 'enable', 0),
            'text' => ConfigService::get('article_watermark', 'text', '杰哥网络科技'),
            'contact' => ConfigService::get('article_watermark', 'contact', 'QQ:2711793818'),
            'position' => ConfigService::get('article_watermark', 'position', 'right_bottom'),
            'opacity' => ConfigService::get('article_watermark', 'opacity', 0.15),
        ];
    }

    public static function setConfig($params)
    {
        ConfigService::set('article_watermark', 'enable', intval($params['enable'] ?? 0));
        ConfigService::set('article_watermark', 'text', $params['text'] ?? '杰哥网络科技');
        ConfigService::set('article_watermark', 'contact', $params['contact'] ?? 'QQ:2711793818');
        ConfigService::set('article_watermark', 'position', $params['position'] ?? 'right_bottom');
        ConfigService::set('article_watermark', 'opacity', floatval($params['opacity'] ?? 0.15));
    }
}