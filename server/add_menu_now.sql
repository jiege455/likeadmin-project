-- ====================================
-- 添加系统开关和邮箱配置菜单
-- 开发者：杰哥网络科技 qq2711793818 杰哥
-- 日期：2026-03-06
-- ====================================

-- 添加系统开关菜单
INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`)
SELECT 
    t.id as pid,
    'C' as type,
    '系统开关' as name,
    'el-icon-Setting' as icon,
    50 as sort,
    'setting.systemSwitch/config' as perms,
    'setting.systemSwitch' as paths,
    'setting/system_switch/index' as component,
    '' as selected,
    '' as params,
    0 as is_cache,
    1 as is_show,
    0 as is_disable,
    UNIX_TIMESTAMP() as create_time,
    UNIX_TIMESTAMP() as update_time
FROM (
    SELECT id FROM `la_system_menu` 
    WHERE name IN ('系统设置', '系统配置', '系统管理') AND type='M' 
    ORDER BY id 
    LIMIT 1
) as t
WHERE NOT EXISTS (
    SELECT 1 FROM `la_system_menu` WHERE name='系统开关' AND paths='setting.systemSwitch'
);

-- 添加系统开关保存按钮
INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`)
SELECT 
    t.id as pid,
    'A' as type,
    '保存配置' as name,
    '' as icon,
    1 as sort,
    'setting.systemSwitch/setConfig' as perms,
    '' as paths,
    '' as component,
    '' as selected,
    '' as params,
    0 as is_cache,
    1 as is_show,
    0 as is_disable,
    UNIX_TIMESTAMP() as create_time,
    UNIX_TIMESTAMP() as update_time
FROM (
    SELECT id FROM `la_system_menu` 
    WHERE name='系统开关' AND paths='setting.systemSwitch' 
    ORDER BY id 
    LIMIT 1
) as t
WHERE NOT EXISTS (
    SELECT 1 FROM `la_system_menu` WHERE name='保存配置' AND perms='setting.systemSwitch/setConfig'
);

-- 添加邮箱配置菜单
INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`)
SELECT 
    t.id as pid,
    'C' as type,
    '邮箱配置' as name,
    'el-icon-Message' as icon,
    75 as sort,
    'setting.email/config' as perms,
    'email' as paths,
    'setting/email/index' as component,
    '' as selected,
    '' as params,
    0 as is_cache,
    1 as is_show,
    0 as is_disable,
    UNIX_TIMESTAMP() as create_time,
    UNIX_TIMESTAMP() as update_time
FROM (
    SELECT id FROM `la_system_menu` 
    WHERE name IN ('系统设置', '系统配置', '系统管理') AND type='M' 
    ORDER BY id 
    LIMIT 1
) as t
WHERE NOT EXISTS (
    SELECT 1 FROM `la_system_menu` WHERE name='邮箱配置' AND paths='email'
);

-- 添加邮箱配置保存按钮
INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`)
SELECT 
    t.id as pid,
    'A' as type,
    '保存' as name,
    '' as icon,
    1 as sort,
    'setting.email/setConfig' as perms,
    '' as paths,
    '' as component,
    '' as selected,
    '' as params,
    0 as is_cache,
    1 as is_show,
    0 as is_disable,
    UNIX_TIMESTAMP() as create_time,
    UNIX_TIMESTAMP() as update_time
FROM (
    SELECT id FROM `la_system_menu` 
    WHERE name='邮箱配置' AND paths='email' 
    ORDER BY id 
    LIMIT 1
) as t
WHERE NOT EXISTS (
    SELECT 1 FROM `la_system_menu` WHERE name='保存' AND perms='setting.email/setConfig'
);

-- 添加邮箱配置测试按钮
INSERT INTO `la_system_menu` (`pid`, `type`, `name`, `icon`, `sort`, `perms`, `paths`, `component`, `selected`, `params`, `is_cache`, `is_show`, `is_disable`, `create_time`, `update_time`)
SELECT 
    t.id as pid,
    'A' as type,
    '发送测试' as name,
    '' as icon,
    2 as sort,
    'setting.email/test' as perms,
    '' as paths,
    '' as component,
    '' as selected,
    '' as params,
    0 as is_cache,
    1 as is_show,
    0 as is_disable,
    UNIX_TIMESTAMP() as create_time,
    UNIX_TIMESTAMP() as update_time
FROM (
    SELECT id FROM `la_system_menu` 
    WHERE name='邮箱配置' AND paths='email' 
    ORDER BY id 
    LIMIT 1
) as t
WHERE NOT EXISTS (
    SELECT 1 FROM `la_system_menu` WHERE name='发送测试' AND perms='setting.email/test'
);

-- 验证菜单是否添加成功
SELECT 
    id, 
    pid, 
    type, 
    name, 
    icon, 
    sort, 
    perms, 
    paths, 
    component, 
    is_show,
    CASE type 
        WHEN 'M' THEN '目录'
        WHEN 'C' THEN '菜单'
        WHEN 'A' THEN '按钮'
        ELSE '未知'
    END as type_name
FROM `la_system_menu` 
WHERE name IN ('系统设置', '系统配置', '系统管理', '系统开关', '邮箱配置') 
ORDER BY pid, sort, id;
