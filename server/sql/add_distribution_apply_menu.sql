-- ----------------------------
-- 添加分销申请菜单
-- ----------------------------

-- 1. 获取“分销管理”菜单的 ID
-- 请确保 '分销管理' 这个名字在您的数据库中是唯一的，且确实存在。
-- 如果不存在，下面的插入语句可能会因为 pid 为空而失效或插入到顶级。
SET @parent_id = (SELECT id FROM la_system_menu WHERE name = '分销管理' LIMIT 1);

-- 2. 插入“分销申请”菜单
INSERT INTO `la_system_menu` 
(`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`) 
VALUES 
(
    IFNULL(@parent_id, 0), -- 如果找不到父级，则默认为顶级菜单（建议手动检查）
    'C',                   -- 类型：菜单
    '分销申请',             -- 名称
    'el-icon-DocumentChecked', -- 图标 (可根据喜好修改)
    0,                     -- 排序
    'distribution.distribution_apply/lists', -- 权限标识 (对应后端控制器方法)
    'distribution/apply',  -- 路由路径
    'distribution/apply/index', -- 组件路径 (对应前端文件 admin/src/views/distribution/apply/index.vue)
    '',                    -- 选中路径高亮
    '',                    -- 参数
    0,                     -- 是否缓存
    1,                     -- 是否显示
    0,                     -- 是否禁用
    UNIX_TIMESTAMP(),      -- 创建时间
    UNIX_TIMESTAMP()       -- 更新时间
);
