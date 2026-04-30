-- 添加待处理审批菜单权限
-- 开发者：杰哥网络科技 qq2711793818 杰哥

-- 查找最大的菜单ID
SELECT @max_id := MAX(id) FROM la_system_menu;

-- 添加待处理审批菜单
INSERT INTO `la_system_menu` (`id`, `pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES
(@max_id + 1, 0, 'C', '待处理审批', 'el-icon-Bell', 999, 'pending_approval/lists', 'pending_approval', 'pending_approval/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 添加待处理审批的按钮权限
INSERT INTO `la_system_menu` (`id`, `pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES
(@max_id + 2, @max_id + 1, 'A', '查看列表', '', 0, 'pending_approval/lists', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@max_id + 3, @max_id + 1, 'A', '统计数据', '', 0, 'pending_approval/statistics', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@max_id + 4, @max_id + 1, 'A', '快捷审批', '', 0, 'pending_approval/audit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@max_id + 5, @max_id + 1, 'A', '审批详情', '', 0, 'pending_approval/detail', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 查看添加结果
SELECT * FROM la_system_menu WHERE perms LIKE 'pending_approval%' ORDER BY id;
