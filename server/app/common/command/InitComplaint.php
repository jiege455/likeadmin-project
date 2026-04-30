<?php
namespace app\common\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;

class InitComplaint extends Command
{
    protected function configure()
    {
        $this->setName('init:complaint')->setDescription('Initialize merchant complaint table and menu');
    }

    protected function execute(Input $input, Output $output)
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
            $output->writeln('Table created successfully.');
            
            // 插入菜单
            $menu = Db::name('system_menu')->where('name', '商家投诉')->find();
            if (!$menu) {
                // 查找商家管理父菜单
                $parent = Db::name('system_menu')->where('name', '商家管理')->find();
                $pid = $parent ? $parent['id'] : 0;

                Db::name('system_menu')->insert([
                    'pid' => $pid,
                    'type' => 'C',
                    'name' => '商家投诉',
                    'icon' => 'el-icon-Warning',
                    'sort' => 50,
                    'perms' => 'merchant:complaint:lists',
                    'paths' => 'merchant/complaint/index',
                    'component' => 'merchant/complaint/index',
                    'is_cache' => 0,
                    'is_show' => 1,
                    'is_disable' => 0,
                    'create_time' => time(),
                    'update_time' => time()
                ]);
                $output->writeln('Menu created successfully.');
            } else {
                $output->writeln('Menu already exists.');
            }
        } catch (\Exception $e) {
            $output->writeln('Error: ' . $e->getMessage());
        }
    }
}
