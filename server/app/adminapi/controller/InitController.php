<?php
namespace app\adminapi\controller;

use think\facade\Db;

class InitController extends BaseAdminController
{
    public function initComplaint()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `la_merchant_complaint` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `user_id` int(11) NOT NULL COMMENT '用户ID',
          `merchant_id` int(11) NOT NULL COMMENT '商家ID',
          `content` text NOT NULL COMMENT '投诉内容',
          `images` text COMMENT '图片凭证',
          `contact` varchar(50) DEFAULT '' COMMENT '联系方式',
          `status` tinyint(1) DEFAULT '0' COMMENT '状态:0=待处理,1=已处理',
          `create_time` int(11) DEFAULT NULL,
          `update_time` int(11) DEFAULT NULL,
          `delete_time` int(11) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `user_id` (`user_id`),
          KEY `merchant_id` (`merchant_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商家投诉表';";

        try {
            Db::execute($sql);
            
            // 插入菜单
            $menu = Db::name('system_menu')->where('name', '商家投诉')->find();
            if (!$menu) {
                Db::name('system_menu')->insert([
                    'pid' => 200, // 假设 200 是商家管理或其他合适的父级ID，需要确认
                    'type' => 'C',
                    'name' => '商家投诉',
                    'icon' => 'el-icon-Warning',
                    'sort' => 50,
                    'perms' => 'merchant:complaint:lists',
                    'paths' => 'merchant/complaint/index',
                    'component' => 'merchant/complaint/index',
                    'is_cache' => 0,
                    'is_show' => 1,
                    'status' => 1,
                    'create_time' => time(),
                    'update_time' => time()
                ]);
                return $this->success('Table and menu created successfully');
            }
            
            return $this->success('Table created, menu already exists');
        } catch (\Exception $e) {
            return $this->fail($e->getMessage());
        }
    }
}
