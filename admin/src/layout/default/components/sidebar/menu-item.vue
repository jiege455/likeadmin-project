<template>
    <template v-if="!route.meta?.hidden">
        <app-link v-if="!hasShowChild" :to="menuLink">
            <el-menu-item :index="routePath">
                <icon
                    class="menu-item-icon"
                    :size="16"
                    v-if="route.meta?.icon"
                    :name="route.meta?.icon"
                />
                <template #title>
                    <span>{{ route.meta?.title }}</span>
                </template>
            </el-menu-item>
        </app-link>
        <el-sub-menu v-else :index="routePath" :popper-class="popperClass">
            <template #title>
                <icon
                    class="menu-item-icon"
                    :size="16"
                    v-if="route.meta?.icon"
                    :name="route.meta?.icon"
                />
                <span>{{ route.meta?.title }}</span>
            </template>
            <menu-item
                v-for="item in visibleChildren"
                :key="item.path"
                :route="item"
                :route-path="resolvePath(item.path)"
                :popper-class="popperClass"
            />
        </el-sub-menu>
    </template>
</template>

<script lang="ts" setup>
/**
 * 开发者公众号：杰哥网络科技
 * qq2711793818 杰哥
 */
import type { RouteRecordRaw } from 'vue-router'

import { getNormalPath, objectToQuery } from '@/utils/util'
import { isExternal } from '@/utils/validate'

interface Props {
    route: RouteRecordRaw
    routePath: string
    popperClass: string
}

const props = defineProps<Props>()

const hasShowChild = computed(() => {
    const children = props.route.children
    if (!children || !children.length) return false
    return children.some((item) => !item.meta?.hidden)
})

const visibleChildren = computed(() => {
    const children = props.route.children
    if (!children || !children.length) return []
    return children.filter((item) => !item.meta?.hidden)
})

const menuLink = computed(() => {
    const query = props.route.meta?.query as string
    let queryStr = ''
    try {
        const queryObj = JSON.parse(query || '{}')
        queryStr = objectToQuery(queryObj)
    } catch (error) {
        queryStr = query || ''
    }
    return `${props.routePath}?${queryStr}`
})

const resolvePath = (path: string) => {
    if (isExternal(path)) {
        return path
    }
    return getNormalPath(`${props.routePath}/${path}`)
}
</script>

<style lang="scss" scoped>
.el-menu-item,
.el-sub-menu__title {
    .menu-item-icon {
        margin-right: 8px;
        width: var(--el-menu-icon-width);
        text-align: center;
        vertical-align: middle;
    }
}
</style>
