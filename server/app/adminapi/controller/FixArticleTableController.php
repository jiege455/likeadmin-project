<?php
/**
 * 修复文章表缺少的字段
 * 开发者：杰哥网络科技
 * QQ：2711793818 杰哥
 * 
 * 安全说明：此接口仅限超级管理员(root=1)访问
 */

namespace app\adminapi\controller;

use app\common\controller\BaseLikeAdminController;
use think\facade\Db;

class FixArticleTableController extends BaseLikeAdminController
{
    public function fix()
    {
        try {
            $adminInfo = $this->request->adminInfo;
            
            if (empty($adminInfo) || $adminInfo['root'] !== 1) {
                return $this->fail('权限不足，仅超级管理员可执行此操作');
            }

            $columns = Db::query("SHOW COLUMNS FROM la_article");
            $columnNames = array_column($columns, 'Field');

            $sqls = [];

            if (!in_array('prev_issue_no', $columnNames)) {
                $sqls[] = "ALTER TABLE `la_article` ADD `prev_issue_no` VARCHAR(20) NOT NULL DEFAULT '' COMMENT '上一期期号' AFTER `issue_no`";
            }

            if (!in_array('prev_issue_content', $columnNames)) {
                $sqls[] = "ALTER TABLE `la_article` ADD `prev_issue_content` TEXT COMMENT '上一期内容' AFTER `hidden_content`";
            }

            if (!in_array('is_series', $columnNames)) {
                $sqls[] = "ALTER TABLE `la_article` ADD `is_series` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否系列文章:0-否,1-是' AFTER `desc`";
            }

            foreach ($sqls as $sql) {
                Db::execute($sql);
            }

            \think\facade\Log::write('文章表修复执行成功，管理员ID: ' . $adminInfo['admin_id'] . '，执行SQL数量: ' . count($sqls), 'info');

            return $this->success('修复成功，已执行' . count($sqls) . '条SQL');
        } catch (\Exception $e) {
            \think\facade\Log::write('文章表修复失败: ' . $e->getMessage(), 'error');
            return $this->fail('修复失败，请联系管理员');
        }
    }
}
