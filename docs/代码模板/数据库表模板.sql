-- ----------------------------
-- {{title}}表
-- ----------------------------
DROP TABLE IF EXISTS `la_{{table_name}}`;
CREATE TABLE `la_{{table_name}}` (
    `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键',
    -- TODO: 填写字段
    -- `name` varchar(64) NOT NULL DEFAULT '' COMMENT '名称',
    -- `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态:1-正常,0-禁用',
    `create_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
    `update_time` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '更新时间',
    `delete_time` int(10) UNSIGNED DEFAULT NULL COMMENT '删除时间',
    PRIMARY KEY (`id`),
    -- TODO: 添加索引
    -- KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='{{title}}表';
