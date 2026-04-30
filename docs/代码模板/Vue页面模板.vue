<!--
 * {{title}}列表页面
 * @author 杰哥
 * @date {{date}}
 * 开发者公众号：杰哥网络科技
 * qq2711793818 杰哥
-->
<template>
    <div class="{{nameLower}}-list">
        <!-- 搜索区域 -->
        <el-card class="!border-none mb-4" shadow="never">
            <el-form :model="queryParams" inline>
                <el-form-item label="关键词">
                    <el-input 
                        v-model="queryParams.keyword" 
                        placeholder="请输入关键词" 
                        clearable 
                        @keyup.enter="getLists"
                    />
                </el-form-item>
                <el-form-item label="状态">
                    <el-select v-model="queryParams.status" placeholder="请选择" clearable>
                        <el-option label="全部" value="" />
                        <el-option label="正常" :value="1" />
                        <el-option label="禁用" :value="0" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="getLists">查询</el-button>
                    <el-button @click="resetQuery">重置</el-button>
                </el-form-item>
            </el-form>
        </el-card>

        <!-- 数据表格 -->
        <el-card class="!border-none" shadow="never">
            <template #header>
                <div class="flex justify-between items-center">
                    <span>{{title}}列表</span>
                    <el-button type="primary" @click="handleAdd">
                        <el-icon><Plus /></el-icon>
                        添加{{title}}
                    </el-button>
                </div>
            </template>

            <el-table v-loading="loading" :data="tableData" size="large">
                <el-table-column prop="id" label="ID" width="80" />
                <!-- TODO: 添加表格列 -->
                <el-table-column prop="status_text" label="状态" width="100">
                    <template #default="{ row }">
                        <el-switch 
                            :model-value="row.status" 
                            :active-value="1" 
                            :inactive-value="0"
                            @change="handleStatusChange(row)"
                        />
                    </template>
                </el-table-column>
                <el-table-column prop="create_time" label="创建时间" width="180" />
                <el-table-column label="操作" width="150" fixed="right">
                    <template #default="{ row }">
                        <el-button type="primary" link @click="handleEdit(row)">编辑</el-button>
                        <el-button type="danger" link @click="handleDelete(row.id)">删除</el-button>
                    </template>
                </el-table-column>
            </el-table>

            <!-- 分页 -->
            <div class="flex justify-end mt-4">
                <el-pagination
                    v-model:current-page="queryParams.page_no"
                    v-model:page-size="queryParams.page_size"
                    :total="total"
                    :page-sizes="[10, 20, 50, 100]"
                    layout="total, sizes, prev, pager, next, jumper"
                    @size-change="getLists"
                    @current-change="getLists"
                />
            </div>
        </el-card>

        <!-- 编辑弹窗 -->
        <edit-popup 
            v-if="showEdit" 
            ref="editRef" 
            :id="currentId"
            @success="getLists"
            @close="showEdit = false"
        />
    </div>
</template>

<script setup lang="ts" name="{{nameLower}}List">
import { ref, reactive, onMounted } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import { Plus } from '@element-plus/icons-vue'
import { get{{name}}Lists, delete{{name}}, change{{name}}Status } from '@/api/{{module}}/{{nameLower}}'
import EditPopup from './components/edit-popup.vue'

// 查询参数
const queryParams = reactive({
    keyword: '',
    status: '' as string | number,
    page_no: 1,
    page_size: 20
})

// 表格数据
const loading = ref(false)
const tableData = ref<any[]>([])
const total = ref(0)

// 弹窗控制
const showEdit = ref(false)
const editRef = ref()
const currentId = ref<number>(0)

// 获取列表数据
const getLists = async () => {
    loading.value = true
    try {
        const res = await get{{name}}Lists(queryParams)
        tableData.value = res.lists
        total.value = res.count
    } catch (error) {
        console.error(error)
    } finally {
        loading.value = false
    }
}

// 重置查询
const resetQuery = () => {
    queryParams.keyword = ''
    queryParams.status = ''
    queryParams.page_no = 1
    getLists()
}

// 添加
const handleAdd = () => {
    currentId.value = 0
    showEdit.value = true
}

// 编辑
const handleEdit = (row: any) => {
    currentId.value = row.id
    showEdit.value = true
}

// 删除
const handleDelete = async (id: number) => {
    await ElMessageBox.confirm('确定要删除该数据吗？', '提示', {
        type: 'warning'
    })
    await delete{{name}}(id)
    ElMessage.success('删除成功')
    getLists()
}

// 切换状态
const handleStatusChange = async (row: any) => {
    try {
        await change{{name}}Status(row.id)
        ElMessage.success('操作成功')
        getLists()
    } catch (error) {
        getLists()
    }
}

// 初始化
onMounted(() => {
    getLists()
})
</script>

<style lang="scss" scoped>
.{{nameLower}}-list {
    // 样式
}
</style>
