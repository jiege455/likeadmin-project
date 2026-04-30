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
namespace app\adminapi\logic\decorate;


use app\common\logic\BaseLogic;
use app\common\model\decorate\DecoratePage;


/**
 * 装修页面
 * Class DecoratePageLogic
 * @package app\adminapi\logic\theme
 */
class DecoratePageLogic extends BaseLogic
{


    /**
     * @notes 获取详情
     * @param $id
     * @return array
     * @author 段誉
     * @date 2022/9/14 18:41
     */
    public static function getDetail($id)
    {
        return DecoratePage::findOrEmpty($id)->toArray();
    }


    /**
     * @notes 获取装修页面列表
     * @return array
     */
    public static function getLists()
    {
        $pages = DecoratePage::field('id,type,name,meta,update_time')
            ->order('id', 'desc')
            ->select()
            ->toArray();
        
        // 【修复】确保每个页面都有可用的名称
        foreach ($pages as &$page) {
            if (empty($page['name']) && !empty($page['meta'])) {
                $meta = json_decode($page['meta'], true);
                if (!empty($meta['name'])) {
                    $page['name'] = $meta['name'];
                }
            }
            // 如果还是没有名称，使用默认值
            if (empty($page['name'])) {
                $page['name'] = $page['type'] == 10 ? '自定义页面' : '未命名页面';
            }
        }
        
        return $pages;
    }

    /**
     * @notes 新增装修页面
     * @param $params
     * @return bool
     */
    public static function add($params)
    {
        try {
            // 将名称存入 meta
            $meta = json_decode($params['meta'] ?? '{}', true);
            $meta['name'] = $params['name'];
            
            DecoratePage::create([
                'type' => 10, // 自定义页面类型
                'name' => $params['name'],
                'data' => $params['data'],
                'meta' => json_encode($meta, JSON_UNESCAPED_UNICODE),
            ]);
            return true;
        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 删除装修页面
     * @param $id
     * @return bool
     */
    public static function del($id)
    {
        try {
            $page = DecoratePage::findOrEmpty($id);
            if ($page->isEmpty()) {
                throw new \Exception('页面不存在');
            }
            // 允许删除的类型：10=自定义页面，4=用户中心
            // 只有首页(type=1)、个人中心(type=2)、PC首页(type=4)、系统风格(type=5)不能删
            if (in_array($page->type, [1, 2, 4, 5])) {
                throw new \Exception('系统核心页面不可删除');
            }
            $page->delete();
            return true;
        } catch (\Exception $e) {
            self::$error = $e->getMessage();
            return false;
        }
    }

    /**
     * @notes 保存装修配置
     * @param $params
     * @return bool
     * @author 段誉
     * @date 2022/9/15 9:37
     */
    public static function save($params)
    {
        $pageData = DecoratePage::where(['id' => $params['id']])->findOrEmpty();
        if ($pageData->isEmpty()) {
            self::$error = '信息不存在';
            return false;
        }
        
        $updateData = [
            'id' => $params['id'],
            'data' => $params['data'],
            'meta' => $params['meta'] ?? '',
        ];
        
        // 【修复】只有非核心页面才允许修改 type
        // 核心页面：首页(1)、个人中心(2)、客服设置(3)、PC首页(4)、系统风格(5)
        if (!in_array($pageData->type, [1, 2, 3, 4, 5])) {
            $updateData['type'] = $params['type'];
        }

        // 【修复】所有页面类型都支持更新名称
        // 从 meta 中提取页面名称（page-meta 组件的 title 字段）
        if (!empty($params['meta'])) {
            $meta = json_decode($params['meta'], true);
            if (!empty($meta) && is_array($meta)) {
                // meta 是数组格式：[{title: "页面名称", ...}]
                $firstMeta = $meta[0] ?? [];
                if (!empty($firstMeta['content']['title'])) {
                    $updateData['name'] = $firstMeta['content']['title'];
                }
            }
        }
        
        // 如果前端明确传递了 name，优先使用前端的值
        if (isset($params['name']) && !empty($params['name'])) {
            $updateData['name'] = $params['name'];
        }

        DecoratePage::update($updateData);
        return true;
    }



}