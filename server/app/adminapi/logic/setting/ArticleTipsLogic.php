<?php
/**
 * 文章提示设置逻辑
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 */
namespace app\adminapi\logic\setting;

use app\common\logic\BaseLogic;
use app\common\service\ConfigService;

class ArticleTipsLogic extends BaseLogic
{
    public static function getConfig()
    {
        return [
            'top_tips' => ConfigService::get('article_tips', 'top_tips', ''),
            'top_tips_show' => ConfigService::get('article_tips', 'top_tips_show', 1),
            'bottom_tips' => ConfigService::get('article_tips', 'bottom_tips', ''),
            'bottom_tips_show' => ConfigService::get('article_tips', 'bottom_tips_show', 1),
        ];
    }

    public static function setConfig($params)
    {
        ConfigService::set('article_tips', 'top_tips', $params['top_tips'] ?? '');
        ConfigService::set('article_tips', 'top_tips_show', intval($params['top_tips_show'] ?? 1));
        ConfigService::set('article_tips', 'bottom_tips', $params['bottom_tips'] ?? '');
        ConfigService::set('article_tips', 'bottom_tips_show', intval($params['bottom_tips_show'] ?? 1));
    }
}
