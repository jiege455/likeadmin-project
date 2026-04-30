<template>
    <div class="p-4">
        <el-tabs v-model="activeName" class="demo-tabs bg-white p-4 rounded" @tab-click="handleClick">
            <el-tab-pane label="实名记录" name="record">
                <record-lists v-if="activeName == 'record'" />
            </el-tab-pane>
            <el-tab-pane label="实名设置" name="config">
                <config-edit v-if="activeName == 'config'" />
            </el-tab-pane>
        </el-tabs>
    </div>
</template>

<script setup lang="ts" name="userRealname">
import { ref } from 'vue'
import RecordLists from './lists.vue'
import ConfigEdit from './config.vue'

const activeName = ref('record')

const handleClick = (tab: any, event: any) => {
    // console.log(tab, event)
}
</script>

<style scoped>
.demo-tabs > .el-tabs__content {
  padding: 32px;
  color: #6b778c;
  font-size: 32px;
  font-weight: 600;
}
</style>
