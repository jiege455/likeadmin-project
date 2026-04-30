-- 开发者：杰哥网络科技 QQ: 2711793818 杰哥
-- 更新商城首页，添加当前商家信息组件
-- 执行时间：2026-03-16

-- 更新商城首页配置
UPDATE `la_decorate_page` 
SET `data` = '[
  {
    "title": "当前商家信息",
    "name": "current-merchant",
    "content": {
      "show_switch": true,
      "show_actions": true,
      "show_tabs": true,
      "show_search": true,
      "show_content_list": true,
      "show_coupon": true,
      "show_apply_btn": true,
      "show_has_merchant": true,
      "enabled": 1
    },
    "styles": {
      "primary_color": "#FF2D3A",
      "title_color": "#333",
      "desc_color": "#666",
      "margin_top": 0,
      "margin_bottom": 10,
      "padding_horizontal": 12,
      "border_radius": 12
    }
  }
]',
`update_time` = UNIX_TIMESTAMP()
WHERE `id` = 1 AND `type` = 1;

-- 更新完成提示
SELECT '商城首页已更新，已添加当前商家信息组件' AS message;
