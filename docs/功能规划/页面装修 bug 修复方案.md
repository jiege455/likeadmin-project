# 页面装修 Bug 修复规划

**开发者：** 杰哥网络科技  
**QQ：** 2711793818 杰哥  
**创建时间：** 2026-03-06  
**Bug 类型：** 数据丢失 + 前端显示异常

---

## 一、Bug 描述

### 1.1 问题现象
1. 后台页面装修页面中，自定义页面的名称显示为"自定义"或"自定义页面{ID}"
2. 点击自定义页面后，页面名称变成"首页"或其他默认名称
3. 重新安装系统后，所有自定义页面数据丢失
4. 前端菜单中仍显示自定义页面，但点击后无法正确加载

### 1.2 问题原因
1. **数据库初始化数据不完整**：一键安装数据库的 SQL 文件中没有包含自定义页面的示例数据
2. **前端容错处理不足**：当 meta 字段为空时，没有正确处理页面名称
3. **数据同步问题**：重新安装后，前端缓存与后端数据不一致

---

## 二、解决方案

### 2.1 数据库层面

#### 方案 A：在 like.sql 中添加自定义页面示例数据

在 `like.sql` 文件的 `la_decorate_page` 记录部分，添加 1-2 个自定义页面的示例数据：

```sql
-- 添加自定义页面示例
INSERT INTO `la_decorate_page` (`id`, `type`, `name`, `data`, `meta`, `create_time`, `update_time`) VALUES 
(100, 10, '活动专题页', '[]', '{"name":"活动专题页"}', UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(101, 10, '促销页面', '[]', '{"name":"促销页面"}', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
```

**优点：**
- 用户安装系统后就有示例自定义页面
- 方便用户理解自定义页面功能

**缺点：**
- 占用少量数据库空间
- 用户可能需要手动删除不需要的示例页面

---

#### 方案 B：修改前端初始化逻辑（推荐）

修改前端代码，在加载自定义页面列表时，如果没有数据则不显示或提示用户创建：

**修改文件：** `admin/src/views/decoration/pages/index.vue`

**修改位置：** 第 287-312 行 `getCustomPages` 函数

**修改内容：**
```typescript
const getCustomPages = async () => {
    try {
        const res = await getDecoratePageList()
        if (res) {
            res.forEach((item: any) => {
                // 排除 PC 首页 (type=4) 和 系统风格 (type=5)
                if (item.type == 4 || item.type == 5) return

                const meta = JSON.parse(item.meta || '{}')
                menus[String(item.id)] = {
                    id: item.id,
                    type: 10,
                    // 优先使用 meta.name，其次使用后端返回的 name，最后使用默认值
                    name: meta.name || item.name || `自定义页面${item.id}`,
                    pageMeta: generatePageData(['page-meta']),
                    pageData: []
                }
            })
        }
    } catch (e) {
        console.error(e)
    }
}
```

**同时修改 getData 函数（第 264-276 行）：**
```typescript
// 从 pageMeta 中恢复页面名称
const pageMeta = menus[String(data.id)].pageMeta
if (pageMeta?.[0]?.content?.title) {
    menus[String(data.id)].name = pageMeta[0].content.title
    
    // 兼容旧数据：如果没有 title_text，则使用 title 作为标题文字
    if (!pageMeta[0].content.title_text) {
        pageMeta[0].content.title_text = pageMeta[0].content.title
    }
} else if (data.name) {
    // 如果 meta 里没有，尝试用后端返回的 name
    menus[String(data.id)].name = data.name
}
```

---

### 2.2 后端层面

#### 修改 DecoratePageLogic.php

**文件：** `server/app/adminapi/logic/decorate/DecoratePageLogic.php`

**修改位置：** `getLists()` 函数（第 47-53 行）

**修改内容：**
```php
public static function getLists()
{
    $pages = DecoratePage::field('id,type,name,meta,update_time')
        ->order('id', 'desc')
        ->select()
        ->toArray();
    
    // 确保每个页面都有可用的名称
    foreach ($pages as &$page) {
        if (empty($page['name']) && !empty($page['meta'])) {
            $meta = json_decode($page['meta'], true);
            if (!empty($meta['name'])) {
                $page['name'] = $meta['name'];
            }
        }
        // 如果还是没有名称，使用默认值
        if (empty($page['name'])) {
            $page['name'] = $page['type'] == 10 ? '自定义页面' : '未命名页面';
        }
    }
    
    return $pages;
}
```

---

### 2.3 前端显示优化

#### 添加空状态提示

**文件：** `admin/src/views/decoration/pages/index.vue`

**修改位置：** 在菜单组件中添加空状态提示

**修改内容：**
在 `Menu` 组件中，如果没有自定义页面，显示提示：

```vue
<template>
    <div class="menu-container">
        <!-- 现有的菜单列表 -->
        <el-tabs v-model="activeMenu" ...>
            <!-- 系统页面 -->
            <el-tab-pane 
                v-for="(menu, key) in systemMenus" 
                :key="key"
                :name="key"
                :label="menu.name"
            />
            
            <!-- 自定义页面 -->
            <el-tab-pane 
                v-for="(menu, key) in customMenus" 
                :key="key"
                :name="key"
                :label="menu.name"
            >
                <template #label>
                    <span>{{ menu.name }}</span>
                    <el-icon 
                        class="close-icon" 
                        @click.stop="handleDelete(menu.id)"
                    >
                        <Close />
                    </el-icon>
                </template>
            </el-tab-pane>
            
            <!-- 空状态提示 -->
            <div v-if="customMenus.length === 0" class="empty-tip">
                <el-empty 
                    description="暂无自定义页面" 
                    :image-size="80"
                >
                    <el-button type="primary" @click="handleAdd">新建页面</el-button>
                </el-empty>
            </div>
        </el-tabs>
        
        <!-- 添加按钮 -->
        <el-button class="add-btn" @click="handleAdd">
            <el-icon><Plus /></el-icon>
            新建自定义页面
        </el-button>
    </div>
</template>
```

---

## 三、数据库修复 SQL

### 3.1 修复现有自定义页面数据

如果你已经安装了系统，但自定义页面数据有问题，可以执行以下 SQL 修复：

```sql
-- 检查是否有自定义页面
SELECT id, type, name, meta FROM la_decorate_page WHERE type = 10;

-- 如果 meta 字段为空，更新 meta 字段
UPDATE la_decorate_page 
SET meta = JSON_OBJECT('name', name) 
WHERE type = 10 AND (meta IS NULL OR meta = '' OR meta = '{}');

-- 验证修复结果
SELECT id, type, name, meta FROM la_decorate_page WHERE type = 10;
```

### 3.2 完整重建自定义页面表

如果数据混乱，可以考虑重建表（**注意：会删除所有自定义页面数据**）：

```sql
-- 备份现有数据（可选）
CREATE TABLE la_decorate_page_backup AS SELECT * FROM la_decorate_page;

-- 删除所有自定义页面
DELETE FROM la_decorate_page WHERE type = 10;

-- 重置自增 ID（可选，如果需要从 100 开始）
ALTER TABLE la_decorate_page AUTO_INCREMENT = 100;

-- 添加一个示例自定义页面
INSERT INTO `la_decorate_page` (`type`, `name`, `data`, `meta`, `create_time`, `update_time`) 
VALUES (10, '活动专题页', '[]', '{"name":"活动专题页"}', UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
```

---

## 四、测试步骤

### 4.1 测试环境准备
1. 备份当前数据库
2. 清空 `la_decorate_page` 表
3. 重新执行 like.sql 初始化数据库

### 4.2 测试流程

#### 测试 1：新建自定义页面
1. 进入后台 → 页面装修
2. 点击"新建自定义页面"按钮
3. 输入页面名称："618 促销活动页"
4. 点击确定
5. **预期结果：**
   - 左侧菜单中出现新页面，名称显示为"618 促销活动页"
   - 点击页面，右侧预览区正常显示
   - 页面顶部显示"618 促销活动页"

#### 测试 2：编辑自定义页面
1. 点击刚创建的自定义页面
2. 添加"轮播图"组件
3. 修改页面设置中的标题为"618 大促"
4. 点击保存
5. **预期结果：**
   - 保存成功提示
   - 左侧菜单中页面名称更新为"618 大促"
   - 刷新页面后，数据不丢失

#### 测试 3：删除自定义页面
1. 点击自定义页面右侧的删除按钮
2. 确认删除
3. **预期结果：**
   - 删除成功提示
   - 左侧菜单中该页面消失
   - 自动切换回首页

#### 测试 4：重新安装系统
1. 创建 2-3 个自定义页面
2. 备份数据库
3. 重新执行 like.sql 安装系统
4. **预期结果：**
   - 自定义页面数据被清空
   - 前端页面装修页面正常显示
   - 不报错，不显示无效的自定义页面

---

## 五、开发阶段划分

### 第一阶段：后端修复（优先级：高）
- [ ] 修改 `DecoratePageLogic.php` 的 `getLists()` 方法
- [ ] 确保返回的页面名称不为空
- [ ] 测试接口返回数据

### 第二阶段：前端修复（优先级：高）
- [ ] 修改 `index.vue` 的 `getCustomPages()` 方法
- [ ] 增加名称容错处理
- [ ] 修改 `getData()` 方法的名称恢复逻辑
- [ ] 测试页面加载和切换

### 第三阶段：数据库修复（优先级：中）
- [ ] 在 like.sql 中添加自定义页面示例数据（可选）
- [ ] 或者提供数据修复 SQL 脚本
- [ ] 测试数据库初始化

### 第四阶段：UI 优化（优先级：低）
- [ ] 添加空状态提示
- [ ] 优化新建页面按钮样式
- [ ] 测试用户体验

---

## 六、文件清单

### 需要修改的文件
1. `server/app/adminapi/logic/decorate/DecoratePageLogic.php` - 后端逻辑
2. `admin/src/views/decoration/pages/index.vue` - 前端主页面
3. `server/public/install/db/like.sql` - 数据库初始化文件（可选）

### 新增文件（可选）
1. `docs/功能规划/页面装修 bug 修复方案.md` - 本文档
2. `sql/fix_decorate_page.sql` - 数据修复 SQL 脚本

---

## 七、注意事项

1. **数据安全**：修改数据库前务必备份
2. **兼容性**：确保修复方案兼容旧数据
3. **测试充分**：每个修改都要充分测试
4. **用户通知**：如果影响现有用户，需要发布更新公告

---

## 八、预期效果

修复后：
1. ✅ 自定义页面名称正确显示
2. ✅ 点击自定义页面正常加载
3. ✅ 重新安装系统后不显示无效的自定义页面
4. ✅ 前端有友好的空状态提示
5. ✅ 后端接口返回的数据健壮性更强

---

**开发者：** 杰哥网络科技  
**QQ：** 2711793818 杰哥  
**更新时间：** 2026-03-06
