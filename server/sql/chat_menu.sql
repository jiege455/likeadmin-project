-- ============================================
-- 聊天管理后台菜单SQL
-- 开发者公众号：杰哥网络科技
-- QQ: 2711793818 杰哥
-- ============================================

-- 添加聊天管理菜单（一级菜单）
INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES
(0, 'M', '聊天管理', 'el-icon-ChatDotRound', 150, '', 'chat', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 获取聊天管理菜单ID
SET @chat_menu_id = LAST_INSERT_ID();

-- 添加聊天室管理菜单（二级菜单）
INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES
(@chat_menu_id, 'C', '聊天室管理', 'el-icon-ChatLineSquare', 1, 'chat.chat_room/lists', 'room', 'chat/room/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

SET @chat_room_id = LAST_INSERT_ID();

-- 聊天室管理权限按钮
INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES
(@chat_room_id, 'A', '新增', '', 1, 'chat.chat_room/add', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@chat_room_id, 'A', '编辑', '', 1, 'chat.chat_room/edit', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@chat_room_id, 'A', '删除', '', 1, 'chat.chat_room/delete', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@chat_room_id, 'A', '状态', '', 1, 'chat.chat_room/status', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

-- 添加消息管理菜单（二级菜单）
INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES
(@chat_menu_id, 'C', '消息管理', 'el-icon-ChatLineRound', 2, 'chat.chat_message/lists', 'message', 'chat/message/index', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());

SET @chat_message_id = LAST_INSERT_ID();

-- 消息管理权限按钮
INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`) VALUES
(@chat_message_id, 'A', '删除', '', 1, 'chat.chat_message/delete', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(@chat_message_id, 'A', '清空', '', 1, 'chat.chat_message/clear', '', '', '', '', 0, 1, 0, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
