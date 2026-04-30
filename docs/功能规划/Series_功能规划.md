# 系列管理功能规划

## 1. 功能概述

### 1.1 目标
为商户提供系列内容管理功能，商户可以创建、编辑、删除系列，管理系列下的期次内容，支持自动发布和手动发布两种模式。

### 1.2 业务场景
- 商户创建系列（如"福彩3D预测系列"），设置系列价格、总期数等
- 商户为系列添加期次内容，每期包含预测内容、开奖号码等
- 用户购买系列后可查看该系列下所有期次内容
- 支持自动发布：系统按设定间隔自动发布期次
- 支持手动发布：商户手动发布期次

### 1.3 与现有系统关系
- 复用 `la_article_cate` 表存储系列数据（通过 `is_series` 字段区分）
- 复用 `la_article` 表存储期次数据（通过 `series_id` 关联）
- 与商户系统集成（`merchant_id` 字段）
- 与订单系统集成（用户购买系列）

## 2. 功能结构

### 2.1 模块划分
- 后台管理模块（adminapi）：管理员管理所有系列
- 商户端模块（api/merchant）：商户管理自己的系列
- 用户端模块（api/series）：用户浏览和购买系列

### 2.2 页面划分
- 系列列表页（series_list.vue）：展示系列列表
- 系列编辑页（series_edit.vue）：创建/编辑系列
- 系列详情页（series_detail.vue）：查看系列详情和期次列表
- 期次编辑页（issue_edit.vue）：创建/编辑期次

### 2.3 接口划分
- 系列列表接口
- 系列详情接口
- 系列保存接口（新增/编辑）
- 系列删除接口
- 系列状态切换接口
- 期次列表接口
- 期次保存接口
- 期次删除接口
- 期次发布接口

## 3. 数据库设计

### 3.1 表结构

#### la_article_cate（文章分类表，复用）
```sql
-- 已有字段，确保包含以下字段：
ALTER TABLE `la_article_cate`
ADD COLUMN `is_series` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否系列:0-否,1-是',
ADD COLUMN `series_price` DECIMAL(10,2) NOT NULL DEFAULT 0.00 COMMENT '系列价格',
ADD COLUMN `total_issues` INT(11) NOT NULL DEFAULT 0 COMMENT '总期数',
ADD COLUMN `auto_publish` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '自动发布:0-否,1-是',
ADD COLUMN `publish_interval` INT(11) NOT NULL DEFAULT 0 COMMENT '发布间隔(秒)',
ADD COLUMN `series_status` TINYINT(1) NOT NULL DEFAULT 1 COMMENT '系列状态:0-下架,1-上架',
ADD COLUMN `lottery_type` VARCHAR(50) DEFAULT '' COMMENT '彩票类型',
ADD COLUMN `series_desc` TEXT COMMENT '系列介绍',
ADD COLUMN `merchant_id` INT(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '所属商户ID',
ADD INDEX `idx_merchant` (`merchant_id`),
ADD INDEX `idx_series` (`is_series`, `series_status`);
```

#### la_article（文章表，复用）
```sql
-- 确保包含以下字段：
ALTER TABLE `la_article`
ADD COLUMN `series_id` INT(11) NOT NULL DEFAULT 0 COMMENT '所属系列ID',
ADD COLUMN `issue_no` INT(11) NOT NULL DEFAULT 0 COMMENT '期次号',
ADD COLUMN `issue_status` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '期次状态:0-未发布,1-已发布',
ADD COLUMN `is_opened` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '是否开奖:0-否,1-是',
ADD COLUMN `open_code` VARCHAR(100) DEFAULT '' COMMENT '开奖号码',
ADD INDEX `idx_series` (`series_id`),
ADD INDEX `idx_issue` (`series_id`, `issue_no`);
```

### 3.2 ER关系
- `la_article_cate` (系列) 1:N `la_article` (期次)
- `la_merchant` (商户) 1:N `la_article_cate` (系列)
- `la_user` (用户) N:M `la_article_cate` (系列购买关系)

## 4. 后端文件规划

### 4.1 控制器
- `app/adminapi/controller/series/SeriesController.php` - 后台系列管理
- `app/adminapi/controller/series/IssueController.php` - 后台期次管理
- `app/api/controller/merchant/SeriesController.php` - 商户端系列管理
- `app/api/controller/series/SeriesController.php` - 用户端系列浏览

### 4.2 模型
- `app/common/model/series/Series.php` - 系列模型
- `app/common/model/series/Issue.php` - 期次模型

### 4.3 逻辑层
- `app/adminapi/logic/series/SeriesLogic.php` - 后台系列业务逻辑
- `app/adminapi/logic/series/IssueLogic.php` - 后台期次业务逻辑
- `app/api/logic/series/SeriesLogic.php` - 用户端系列业务逻辑
- `app/api/logic/series/IssueLogic.php` - 用户端期次业务逻辑

### 4.4 列表类
- `app/adminapi/lists/series/SeriesLists.php` - 后台系列列表
- `app/adminapi/lists/series/IssueLists.php` - 后台期次列表

### 4.5 验证器
- `app/adminapi/validate/series/SeriesValidate.php` - 系列数据验证
- `app/adminapi/validate/series/IssueValidate.php` - 期次数据验证

## 5. 前端文件规划

### 5.1 页面
- `uniapp/src/packages/pages/merchant/series_list.vue` - 商户端系列列表
- `uniapp/src/packages/pages/merchant/series_edit.vue` - 商户端系列编辑
- `uniapp/src/packages/pages/merchant/series_detail.vue` - 商户端系列详情
- `uniapp/src/packages/pages/merchant/issue_edit.vue` - 商户端期次编辑

### 5.2 API封装
- `uniapp/src/api/series.ts` - 系列相关API

### 5.3 路由
- 在 `uniapp/src/pages.json` 中配置系列管理相关路由

## 6. 接口规划

### 6.1 商户端系列管理接口

| API路径 | 方法 | 参数 | 返回值 | 权限 |
|---------|------|------|--------|------|
| /merchant.series/lists | GET | page_no, page_size, keyword, lottery_type, series_status | {lists: [], count: 0} | 商户登录 |
| /merchant.series/detail | GET | id | {info: {}, issues: []} | 商户登录 |
| /merchant.series/save | POST | id, name, lottery_type, series_price, total_issues, series_desc, auto_publish, publish_interval, series_status | {msg: '保存成功'} | 商户登录 |
| /merchant.series/delete | POST | id | {msg: '删除成功'} | 商户登录 |
| /merchant.series/status | POST | id, status | {msg: '操作成功'} | 商户登录 |

### 6.2 商户端期次管理接口

| API路径 | 方法 | 参数 | 返回值 | 权限 |
|---------|------|------|--------|------|
| /merchant.issue/lists | GET | series_id, page_no, page_size | {lists: [], count: 0} | 商户登录 |
| /merchant.issue/detail | GET | id | {info: {}} | 商户登录 |
| /merchant.issue/save | POST | id, series_id, issue_no, title, content, open_code, is_opened | {msg: '保存成功'} | 商户登录 |
| /merchant.issue/delete | POST | id | {msg: '删除成功'} | 商户登录 |
| /merchant.issue/publish | POST | id | {msg: '发布成功'} | 商户登录 |

### 6.3 用户端系列浏览接口

| API路径 | 方法 | 参数 | 返回值 | 权限 |
|---------|------|------|--------|------|
| /series/lists | GET | page_no, page_size, lottery_type | {lists: []} | 无 |
| /series/detail | GET | id | {info: {}, issues: [], has_permission: false} | 无 |

## 7. 测试步骤

### 7.1 创建系列测试
1. 登录商户端
2. 进入系列管理页面
3. 点击"创建系列"
4. 填写系列信息：名称、彩票类型、价格、总期数、介绍
5. 设置自动发布和发布间隔
6. 点击保存
7. 预期：系列创建成功，返回列表页

### 7.2 编辑系列测试
1. 在系列列表点击某个系列的"编辑"
2. 修改系列信息
3. 点击保存
4. 预期：系列更新成功

### 7.3 添加期次测试
1. 进入系列详情页
2. 点击"发布期次"
3. 填写期次信息：期次号、标题、内容
4. 点击保存
5. 预期：期次创建成功

### 7.4 删除系列测试
1. 在系列列表点击某个系列的删除按钮
2. 确认删除
3. 预期：系列删除成功（如果无期次）

### 7.5 状态切换测试
1. 在系列列表切换某个系列的状态
2. 预期：状态更新成功

## 8. 开发阶段划分

### 阶段一：数据库和模型（优先级：高）✅ 已完成
- ✅ 检查并更新数据库表结构
- ✅ 创建 Series 和 Issue 模型

### 阶段二：后端接口（优先级：高）✅ 已完成
- ✅ 重写商户端系列管理接口
- ✅ 重写商户端期次管理接口
- ✅ 添加数据验证

### 阶段三：前端页面（优先级：高）✅ 已完成
- ✅ 重写系列列表页
- ✅ 重写系列编辑页
- ✅ 新增系列详情页
- ✅ 新增期次编辑页
- ✅ 更新pages.json路由配置

### 阶段四：测试和优化（优先级：中）⏳ 待测试
- ⏳ 功能测试
- ⏳ Bug修复
- ⏳ 性能优化

## 9. 开发者信息
开发者：杰哥网络科技
QQ：2711793818
