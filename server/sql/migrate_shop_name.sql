-- =====================================================
-- 数据库迁移脚本：统一商户名称字段
-- 开发者：杰哥网络科技
-- QQ：2711793818 杰哥
-- 说明：将 shop_name 的值同步到 name，后续废弃 shop_name 字段
-- 执行时间：2026-02-24
-- =====================================================

-- 1. 先查看当前数据情况（可选，用于检查）
-- SELECT id, name, shop_name FROM la_merchant WHERE shop_name != '' AND shop_name IS NOT NULL;

-- 2. 将 shop_name 的值同步到 name（如果 shop_name 有值）
UPDATE la_merchant 
SET name = shop_name 
WHERE shop_name != '' AND shop_name IS NOT NULL;

-- 3. 清空 shop_name 字段（可选，也可以直接删除字段）
-- ALTER TABLE la_merchant DROP COLUMN shop_name;

-- 执行完成后，所有商户名称统一使用 name 字段
