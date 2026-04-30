<?php
// +----------------------------------------------------------------------
// | likeadmin 快速开发前后端分离管理后台（PHP 版）
// +----------------------------------------------------------------------
// | 欢迎阅读学习系统程序代码，建议反馈是我们前进的动力
// | 开源版本可自由商用，可去除界面版权 logo
// | gitee 下载：https://gitee.com/likeshop_gitee/likeadmin
// | github 下载：https://github.com/likeshop-github/likeadmin
// | 访问官网：https://www.likeadmin.cn
// | likeadmin 团队 版权所有 拥有最终解释权
// +----------------------------------------------------------------------
// | author: likeadminTeam
// +----------------------------------------------------------------------

namespace app\adminapi\logic;


use app\common\logic\BaseLogic;
use app\common\service\ConfigService;
use app\common\service\FileService;
use think\facade\Db;


/**
 * 工作台
 * Class WorkbenchLogic
 * @package app\adminapi\logic
 */
class WorkbenchLogic extends BaseLogic
{
    /**
     * @notes 工作套
     * @param $adminInfo
     * @return array
     * @author 段誉
     * @date 2021/12/29 15:58
     */
    public static function index()
    {
        return [
            // 版本信息
            'version' => self::versionInfo(),
            // 今日数据
            'today' => self::today(),
            // 常用功能
            'menu' => self::menu(),
            // 近15日访客数
            'visitor' => self::visitor(),
            // 服务支持
            'support' => self::support(),
            // 销售数据
            'sale' => self::sale(),
            // 待处理事项
            'pending' => self::pending()
        ];
    }

    /**
     * @notes 待处理事项统计
     * @return array
     * @author 杰哥网络科技
     */
    public static function pending(): array
    {
        $pendingItems = [];

        $distributionApplyPending = Db::name('distribution_apply')
            ->where('status', 0)
            ->whereNull('delete_time')
            ->count();

        if ($distributionApplyPending > 0) {
            $pendingItems[] = [
                'name' => '分销员申请',
                'count' => $distributionApplyPending,
                'url' => '/distribution/apply',
                'icon' => 'el-icon-UserFilled',
                'type' => 'success'
            ];
        }

        $merchantApplyPending = Db::name('merchant_apply')
            ->where('status', 0)
            ->whereNull('delete_time')
            ->count();

        if ($merchantApplyPending > 0) {
            $pendingItems[] = [
                'name' => '商家入驻申请',
                'count' => $merchantApplyPending,
                'url' => '/merchant/apply',
                'icon' => 'el-icon-Shop',
                'type' => 'warning'
            ];
        }

        $articleAuditPending = Db::name('article')
            ->where('audit_status', 0)
            ->whereNull('delete_time')
            ->count();

        if ($articleAuditPending > 0) {
            $pendingItems[] = [
                'name' => '文章审核',
                'count' => $articleAuditPending,
                'url' => '/article/audit',
                'icon' => 'el-icon-Document',
                'type' => 'primary'
            ];
        }

        $withdrawApplyPending = Db::name('withdraw_apply')
            ->where('status', 0)
            ->whereNull('delete_time')
            ->count();

        if ($withdrawApplyPending > 0) {
            $pendingItems[] = [
                'name' => '提现申请',
                'count' => $withdrawApplyPending,
                'url' => '/finance/merchant_withdraw',
                'icon' => 'el-icon-Money',
                'type' => 'danger'
            ];
        }

        return $pendingItems;
    }


    /**
     * @notes 常用功能
     * @return array[]
     * @author 段誉
     * @date 2021/12/29 16:40
     */
    public static function menu(): array
    {
        return [
            [
                'name' => '管理员',
                'image' => FileService::getFileUrl(config('project.default_image.menu_admin')),
                'url' => '/permission/admin'
            ],
            [
                'name' => '角色管理',
                'image' => FileService::getFileUrl(config('project.default_image.menu_role')),
                'url' => '/permission/role'
            ],
            [
                'name' => '部门管理',
                'image' => FileService::getFileUrl(config('project.default_image.menu_dept')),
                'url' => '/organization/department'
            ],
            [
                'name' => '字典管理',
                'image' => FileService::getFileUrl(config('project.default_image.menu_dict')),
                'url' => '/setting/dev_tools/dict'
            ],
            [
                'name' => '代码生成器',
                'image' => FileService::getFileUrl(config('project.default_image.menu_generator')),
                'url' => '/dev_tools/code'
            ],
            [
                'name' => '素材中心',
                'image' => FileService::getFileUrl(config('project.default_image.menu_file')),
                'url' => '/app/material/index'
            ],
            [
                'name' => '菜单权限',
                'image' => FileService::getFileUrl(config('project.default_image.menu_auth')),
                'url' => '/permission/menu'
            ],
            [
                'name' => '网站信息',
                'image' => FileService::getFileUrl(config('project.default_image.menu_web')),
                'url' => '/setting/website/information'
            ],
        ];
    }


    /**
     * @notes 版本信息
     * @return array
     * @author 段誉
     * @date 2021/12/29 16:08
     */
    public static function versionInfo(): array
    {
        return [
            'version' => config('project.version'),
            'website' => config('project.website.url'),
            'name' => ConfigService::get('website', 'name'),
            'based' => 'vue3.x、ElementUI、MySQL',
            'channel' => [
                'website' => 'https://www.likeadmin.cn',
                'gitee' => 'https://gitee.com/likeadmin/likeadmin_php',
            ]
        ];
    }


    /**
     * @notes 今日数据
     * @return int[]
     * @author 段誉
     * @date 2021/12/29 16:15
     */
    public static function today(): array
    {
        $today_start = strtotime(date('Y-m-d 00:00:00'));
        $now = time();

        // 查询今日销售额和总销售额（文章订单 + 系列订单 + 充值订单）
        $today_sales = Db::name('article_order')
                ->where('pay_status', 1)
                ->where('pay_time', '>=', $today_start)
                ->where('pay_time', '<=', $now)
                ->sum('order_amount') ?? 0;
        
        $today_sales += Db::name('series_order')
                ->where('pay_status', 1)
                ->where('pay_time', '>=', $today_start)
                ->where('pay_time', '<=', $now)
                ->sum('order_amount') ?? 0;
        
        $today_sales += Db::name('recharge_order')
                ->where('pay_status', 1)
                ->where('pay_time', '>=', $today_start)
                ->where('pay_time', '<=', $now)
                ->sum('order_amount') ?? 0;

        $total_sales = Db::name('article_order')
                ->where('pay_status', 1)
                ->sum('order_amount') ?? 0;
        
        $total_sales += Db::name('series_order')
                ->where('pay_status', 1)
                ->sum('order_amount') ?? 0;
        
        $total_sales += Db::name('recharge_order')
                ->where('pay_status', 1)
                ->sum('order_amount') ?? 0;

        // 查询今日订单量和总订单量
        $today_order_num = Db::name('article_order')
                ->where('pay_status', 1)
                ->where('pay_time', '>=', $today_start)
                ->where('pay_time', '<=', $now)
                ->count();
        
        $today_order_num += Db::name('series_order')
                ->where('pay_status', 1)
                ->where('pay_time', '>=', $today_start)
                ->where('pay_time', '<=', $now)
                ->count();
        
        $today_order_num += Db::name('recharge_order')
                ->where('pay_status', 1)
                ->where('pay_time', '>=', $today_start)
                ->where('pay_time', '<=', $now)
                ->count();

        $total_order_sum = Db::name('article_order')
                ->where('pay_status', 1)
                ->count();
        
        $total_order_sum += Db::name('series_order')
                ->where('pay_status', 1)
                ->count();
        
        $total_order_sum += Db::name('recharge_order')
                ->where('pay_status', 1)
                ->count();

        // 查询今日新增用户和总用户
        $today_new_user = Db::name('user')
                ->where('create_time', '>=', $today_start)
                ->where('create_time', '<=', $now)
                ->count();

        $total_user = Db::name('user')->count();

        // 查询今日访问量和总访问量（用户会话）
        $today_visitor = Db::name('user_session')
                ->where('update_time', '>=', $today_start)
                ->where('update_time', '<=', $now)
                ->count();

        $total_visitor = Db::name('user_session')->count();

        return [
            'time' => date('Y-m-d H:i:s'),
            // 今日销售额
            'today_sales' => round($today_sales, 2),
            // 总销售额
            'total_sales' => round($total_sales, 2),

            // 今日访问量
            'today_visitor' => $today_visitor,
            // 总访问量
            'total_visitor' => $total_visitor,

            // 今日新增用户量
            'today_new_user' => $today_new_user,
            // 总用户量
            'total_new_user' => $total_user,

            // 订单量 (笔)
            'order_num' => $today_order_num,
            // 总订单量
            'order_sum' => $total_order_sum
        ];
    }


    /**
     * @notes 访问数
     * @return array
     * @author 段誉
     * @date 2021/12/29 16:57
     */
    public static function visitor(): array
    {
        $num = [];
        $date = [];
        
        // 获取近 15 天的访客数据
        for ($i = 14; $i >= 0; $i--) {
            $day_start = strtotime(date('Y-m-d', strtotime("-{$i} days")) . ' 00:00:00');
            $day_end = strtotime(date('Y-m-d', strtotime("-{$i} days")) . ' 23:59:59');
            
            $date[] = date('m/d', $day_start);
            
            // 查询当天的访客数（从 user_session 表统计）
            $visitor_count = Db::name('user_session')
                ->where('update_time', '>=', $day_start)
                ->where('update_time', '<=', $day_end)
                ->count();
            
            $num[] = $visitor_count;
        }

        return [
            'date' => $date,
            'list' => [
                ['name' => '访客数', 'data' => $num]
            ]
        ];
    }

    /**
     * @notes 销售数据
     * @return array
     * @author 段誉
     * @date 2021/12/29 16:57
     */
    public static function sale(): array
    {
        $num = [];
        $date = [];
        
        // 获取近 7 天的销售数据
        for ($i = 6; $i >= 0; $i--) {
            $day_start = strtotime(date('Y-m-d', strtotime("-{$i} days")) . ' 00:00:00');
            $day_end = strtotime(date('Y-m-d', strtotime("-{$i} days")) . ' 23:59:59');
            
            $date[] = date('m/d', $day_start);
            
            // 查询当天的销售额（文章订单 + 系列订单 + 充值订单）
            $day_sales = Db::name('article_order')
                ->where('pay_status', 1)
                ->where('pay_time', '>=', $day_start)
                ->where('pay_time', '<=', $day_end)
                ->sum('order_amount') ?? 0;
            
            $day_sales += Db::name('series_order')
                ->where('pay_status', 1)
                ->where('pay_time', '>=', $day_start)
                ->where('pay_time', '<=', $day_end)
                ->sum('order_amount') ?? 0;
            
            $day_sales += Db::name('recharge_order')
                ->where('pay_status', 1)
                ->where('pay_time', '>=', $day_start)
                ->where('pay_time', '<=', $day_end)
                ->sum('order_amount') ?? 0;
            
            // 转换为"万"为单位
            $num[] = round($day_sales / 10000, 2);
        }

        return [
            'date' => $date,
            'list' => [
                ['name' => '销售量', 'data' => $num]
            ]
        ];
    }


    /**
     * @notes 服务支持
     * @return array[]
     * @author 段誉
     * @date 2022/7/18 11:18
     */
    public static function support()
    {
        return [
            [
                'image' => FileService::getFileUrl(config('project.default_image.qq_group')),
                'title' => '官方公众号',
                'desc' => '关注官方公众号',
            ],
            [
                'image' => FileService::getFileUrl(config('project.default_image.customer_service')),
                'title' => '添加企业客服微信',
                'desc' => '想了解更多请添加客服',
            ]
        ];
    }

}