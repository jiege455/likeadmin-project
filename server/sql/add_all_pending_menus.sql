-- 添加待处理审批和相关功能的菜单权限
-- 开发者：杰哥网络科技 qq2711793818 杰哥

-- 获取最大菜单ID
SET @max_id = (SELECT MAX(id) FROM la_system_menu);

-- 1. 添加待处理审批菜单
INSERT INTO `la_system_menu` (`id`, `pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES
(@max_id + 1, 0, 'C', '待处理审批', 'el-icon-Bell', 999, 'pending_approval/lists', 'pending_approval', 'pending_approval/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 添加待处理审批的按钮权限
INSERT INTO `la_system_menu` (`id`, `pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES
(@max_id + 2, @max_id + 1, 'A', '查看列表', '', 0, 'pending_approval/lists', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@max_id + 3, @max_id + 1, 'A', '统计数据', '', 0, 'pending_approval/statistics', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@max_id + 4, @max_id + 1, 'A', '快捷审批', '', 0, 'pending_approval/audit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@max_id + 5, @max_id + 1, 'A', '审批详情', '', 0, 'pending_approval/detail', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 2. 检查并添加文章审核菜单（如果不存在）
INSERT INTO `la_system_menu` (`id`, `pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`)
SELECT @max_id + 10, 0, 'C', '文章审核', 'el-icon-Document', 50, 'article.audit/lists', 'article/audit', 'article/audit/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM la_system_menu WHERE paths = 'article/audit');

-- 添加文章审核的按钮权限
INSERT INTO `la_system_menu` (`id`, `pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`)
SELECT @max_id + 11, @max_id + 10, 'A', '查看列表', '', 0, 'article.audit/lists', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM la_system_menu WHERE perms = 'article.audit/lists' AND type = 'A');

INSERT INTO `la_system_menu` (`id`, `pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`)
SELECT @max_id + 12, @max_id + 10, 'A', '审核文章', '', 0, 'article.audit/audit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM la_system_menu WHERE perms = 'article.audit/audit' AND type = 'A');

-- 3. 检查并添加商家提现管理菜单（如果不存在）
INSERT INTO `la_system_menu` (`id`, `pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`)
SELECT @max_id + 20, 0, 'C', '商家提现', 'el-icon-Money', 60, 'finance.merchant_withdraw/lists', 'finance/merchant_withdraw', 'finance/merchant_withdraw', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM la_system_menu WHERE paths = 'finance/merchant_withdraw');

-- 添加商家提现的按钮权限
INSERT INTO `la_system_menu` (`id`, `pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`)
SELECT @max_id + 21, @max_id + 20, 'A', '查看列表', '', 0, 'finance.merchant_withdraw/lists', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM la_system_menu WHERE perms = 'finance.merchant_withdraw/lists' AND type = 'A');

INSERT INTO `la_system_menu` (`id`, `pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`)
SELECT @max_id + 22, @max_id + 20, 'A', '审核提现', '', 0, 'finance.merchant_withdraw/audit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM la_system_menu WHERE perms = 'finance.merchant_withdraw/audit' AND type = 'A');

-- 查看添加结果
SELECT id, pid, type, name, perms, paths, component FROM la_system_menu 
WHERE perms LIKE 'pending_approval%' 
   OR perms LIKE 'article.audit%' 
   OR perms LIKE 'finance.merchant_withdraw%'
ORDER BY id;
