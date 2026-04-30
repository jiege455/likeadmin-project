-- 开发者：杰哥网络科技 QQ: 2711793818 杰哥
-- 更新个人中心默认配置（用于like.sql初始化文件）
-- 执行时间：2026-03-16

-- 个人中心默认配置：用户信息 + 商家中心入口 + 我的服务（去掉广告图）
-- 页面背景改为白色

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
`update_time` = UNIX_TIMESTAMP()
WHERE `id` = 2 AND `type` = 2;

-- 验证更新结果
SELECT '个人中心配置已更新' AS message, `id`, `name` FROM `la_decorate_page` WHERE `id` = 2;
