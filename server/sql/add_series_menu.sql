-- 添加系列管理菜单
-- 请在后台 -> 权限管理 -> 菜单管理 中执行以下SQL，或直接在数据库中执行

-- 1. 先查找内容管理的ID
SET @content_pid = (SELECT id FROM la_system_menu WHERE name = '内容管理' LIMIT 1);

-- 2. 添加系列管理菜单（如果内容管理存在）
INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`) 
VALUES 
(@content_pid, 'C', '系列管理', 'el-icon-List', 90, 'series.series/lists', 'series/list', 'series/list', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 3. 获取刚插入的系列管理菜单ID
SET @series_id = LAST_INSERT_ID();

-- 4. 添加系列管理的子权限
INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`) 
VALUES 
(@series_id, 'A', '添加', '', 1, 'series.series/add', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@series_id, 'A', '编辑', '', 1, 'series.series/edit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@series_id, 'A', '删除', '', 1, 'series.series/delete', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@series_id, 'A', '状态', '', 1, 'series.series/status', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 5. 添加期次管理菜单
INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`) 
VALUES 
(@series_id, 'C', '期次管理', 'el-icon-Document', 80, 'series.issue/lists', 'series/issue', 'series/issue/list', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 6. 获取期次管理菜单ID
SET @issue_id = LAST_INSERT_ID();

-- 7. 添加期次管理的子权限
INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`) 
VALUES 
(@issue_id, 'A', '添加', '', 1, 'series.issue/add', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@issue_id, 'A', '编辑', '', 1, 'series.issue/edit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@issue_id, 'A', '删除', '', 1, 'series.issue/delete', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@issue_id, 'A', '发布', '', 1, 'series.issue/publish', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 完成后刷新后台页面即可看到系列管理菜单
