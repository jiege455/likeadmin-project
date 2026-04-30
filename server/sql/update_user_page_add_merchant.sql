-- 开发者：杰哥网络科技 QQ: 2711793818 杰哥
-- 更新个人中心页面，添加商家中心入口组件，去掉广告图
-- 执行时间：2026-03-15

-- 更新个人中心页面配置（在用户信息组件后面添加商家中心入口，去掉广告图）
UPDATE `la_decorate_page` 
SET `data` = '[
  {
    "title": "用户信息",
    "name": "user-info",
    "disabled": 1,
    "content": {
      "background_type": 2,
      "background_color": "#ffffff",
      "text_color": "#333333"
    },
    "styles": {}
  },
  {
    "title": "商家中心入口",
    "name": "merchant-center",
    "content": {
      "enabled": 1
    },
    "styles": {
      "margin_top": 10,
      "margin_bottom": 10,
      "padding_horizontal": 12,
      "border_radius": 12,
      "primary_color": "#EF4444"
    }
  },
  {
    "title": "我的服务",
    "name": "my-service",
    "content": {
      "style": 1,
      "title": "我的服务",
      "data": [
        {
          "image": "/resource/image/adminapi/default/user_collect.png",
          "name": "我的收藏",
          "link": {
            "path": "/pages/collection/collection",
            "name": "我的收藏",
            "type": "shop"
          },
          "is_show": "1"
        },
        {
          "image": "/resource/image/adminapi/default/user_setting.png",
          "name": "个人设置",
          "link": {
            "path": "/pages/user_set/user_set",
            "name": "个人设置",
            "type": "shop"
          },
          "is_show": "1"
        },
        {
          "image": "/resource/image/adminapi/default/user_kefu.png",
          "name": "联系客服",
          "link": {
            "path": "/pages/customer_service/customer_service",
            "name": "联系客服",
            "type": "shop"
          },
          "is_show": "1"
        },
        {
          "image": "/resource/image/adminapi/default/wallet.png",
          "name": "我的钱包",
          "link": {
            "path": "/packages/pages/user_wallet/user_wallet",
            "name": "我的钱包",
            "type": "shop"
          },
          "is_show": "1"
        }
      ],
      "enabled": 1
    },
    "styles": {}
  }
]',
`meta` = '[
  {
    "title": "页面设置",
    "name": "page-meta",
    "content": {
      "title": "个人中心",
      "bg_type": "1",
      "bg_color": "#ffffff",
      "bg_image": "",
      "text_color": "1",
      "title_type": "2",
      "title_img": "/resource/image/adminapi/default/page_mate_title.png"
    },
    "styles": {}
  }
]',
`update_time` = 1710933097
WHERE `id` = 2 AND `type` = 2;

-- 更新完成提示
SELECT '个人中心页面已更新，已添加商家中心入口组件，已去掉广告图' AS message;
