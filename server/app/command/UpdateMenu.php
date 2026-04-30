<?php
// 调整菜单结构脚本
// 开发者：杰哥网络科技
// QQ: 2711793818 杰哥

namespace app\command;

use think\console\Command;
use think\console\Input;
use think\console\Output;
use think\facade\Db;

class UpdateMenu extends Command
{
    protected function configure()
    {
        $this->setName('update_menu')
            ->setDescription('调整菜单结构 - 将商户文章移到商家管理下');
    }

    protected function execute(Input $input, Output $output)
    {
        // 查询当前菜单结构
        $menus = Db::name('admin_menu')
            ->where('title', 'like', '%商户%')
            ->whereOr('title', 'like', '%商家%')
            ->whereOr('title', 'like', '%文章管理%')
            ->order('parent_id, sort')
            ->select()
            ->toArray();
        
        $output->writeln(str_repeat('-', 80));
        $output->writeln(sprintf("%-6s %-8s %-20s %-30s %-6s", 'ID', 'Parent', '标题', '标识', '排序'));
        $output->writeln(str_repeat('-', 80));
        
        foreach ($menus as $menu) {
            $output->writeln(sprintf("%-6s %-8s %-20s %-30s %-6s", 
                $menu['id'], 
                $menu['parent_id'], 
                $menu['title'], 
                $menu['name'],
                $menu['sort']
            ));
        }
        
        $output->writeln(str_repeat('-', 80));
        $output->writeln('');
        
        // 查找关键菜单
        $merchant_manage = Db::name('admin_menu')->where('title', '商家管理')->find();
        $article_manage = Db::name('admin_menu')->where('title', '文章管理')->find();
        $merchant_article = Db::name('admin_menu')->where('title', '商户文章')->find();
        
        $output->writeln("关键菜单 ID:");
        $output->writeln("- 商家管理 ID: " . ($merchant_manage['id'] ?? '未找到'));
        $output->writeln("- 文章管理 ID: " . ($article_manage['id'] ?? '未找到'));
        $output->writeln("- 商户文章 ID: " . ($merchant_article['id'] ?? '未找到'));
        $output->writeln('');
        
        if ($merchant_manage && $merchant_article) {
            $output->writeln("准备执行调整:");
            $output->writeln("将 [商户文章] (ID: {$merchant_article['id']}) 移动到 [商家管理] (ID: {$merchant_manage['id']}) 下");
            $output->writeln('');
            
            // 执行更新
            $result = Db::name('admin_menu')
                ->where('id', $merchant_article['id'])
                ->update([
                    'parent_id' => $merchant_manage['id'],
                    'sort' => 10
                ]);
            
            if ($result) {
                $output->writeln("✅ 菜单调整成功!");
                
                // 调整其他菜单排序
                Db::name('admin_menu')->where('title', '商户列表')->update(['sort' => 20]);
                Db::name('admin_menu')->where('title', '商户审核')->update(['sort' => 30]);
                Db::name('admin_menu')->where('title', '商户投诉')->update(['sort' => 40]);
                Db::name('admin_menu')->where('title', '商户提现')->update(['sort' => 50]);
                
                $output->writeln("✅ 菜单排序已调整!");
                
                // 清理缓存
                Db::name('admin_config')->where('name', 'admin_menu_cache')->delete();
                $output->writeln("✅ 缓存已清理!");
                $output->writeln('');
                $output->writeln("请重新登录后台查看效果。");
            } else {
                $output->writeln("❌ 菜单调整失败!");
            }
        } else {
            $output->writeln("❌ 未找到相关菜单，请手动检查数据库。");
        }
        
        return 0;
    }
}
