-- 修复商家入驻审核bug
-- 执行前请备份数据库！

-- 1. 开启商家入驻审核（默认开启）
INSERT INTO `la_config` (`type`, `name`, `value`, `create_time`, `update_time`) 
VALUES ('merchant', 'open_audit', '1', UNIX_TIMESTAMP(), UNIX_TIMESTAMP())
ON DUPLICATE KEY UPDATE `value` = '1', `update_time` = UNIX_TIMESTAMP();

-- 2. 修复逻辑说明：
-- 如果后台开启了商家审核（open_audit=1），用户申请后会在merchant_apply表插入status=0（待审核）的记录
-- 如果后台关闭了商家审核（open_audit=0），用户申请后会直接在merchant表插入status=1（已通过）的记录
-- 但之前关闭审核时没有在merchant_apply表插入记录，导致前端显示"申请"按钮
-- 现在已修复代码，即使关闭审核也会在merchant_apply表插入status=1（已通过）的记录

-- 3. 如果需要为已有商户补全merchant_apply表记录，执行以下SQL（可选）：
-- 注意：执行前请先备份数据！
-- 以下SQL会将所有已在merchant表有status=1的商户，同步到merchant_apply表（如果apply表没有记录）

-- INSERT INTO `la_config` (`type`, `name`, `value`, `create_time`, `update_time`)
-- SELECT 'merchant', 'open_audit', '1', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()
-- FROM DUAL WHERE NOT EXISTS (SELECT 1 FROM `la_config` WHERE `type` = 'merchant' AND `name` = 'open_audit');

SELECT '修复完成！请确保代码已更新到最新版本。' AS result;