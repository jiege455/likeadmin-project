<template>
    <div class="article-editor">
        <!-- #ifdef H5 -->
        <div style="border: 1px solid #ccc">
            <Toolbar
                style="border-bottom: 1px solid #ccc"
                :editor="editorRef"
                :defaultConfig="toolbarConfig"
                :mode="mode"
            />
            <Editor
                style="height: 500px; overflow-y: hidden"
                v-model="valueHtml"
                :defaultConfig="editorConfig"
                :mode="mode"
                @onCreated="handleCreated"
                @onChange="handleChange"
            />
        </div>
        <!-- #endif -->
        <!-- #ifndef H5 -->
        <div style="padding: 20px; text-align: center; color: #999">
            移动端暂不支持富文本编辑，请在电脑端操作
        </div>
        <!-- #endif -->
    </div>
</template>

<script setup lang="ts">
/**
 * ArticleEditor 文章发表/编辑富文本编辑器
 *
 * Props:
 * - content: 初始内容
 *
 * Events:
 * - update:content: 内容变化时触发
 */
// #ifdef H5
import '@wangeditor/editor/dist/css/style.css'
import { Editor, Toolbar } from '@wangeditor/editor-for-vue'
// #endif

import { onBeforeUnmount, ref, shallowRef, watch } from 'vue'

const props = defineProps<{
    content?: string
}>()

const emit = defineEmits<{
    (e: 'update:content', content: string): void
}>()

const editorRef = shallowRef()
const valueHtml = ref(props.content || '')
const mode = 'default'

watch(
    () => props.content,
    (newVal) => {
        if (newVal !== valueHtml.value) {
            valueHtml.value = newVal || ''
        }
    }
)

const toolbarConfig = {}
const editorConfig = {
    placeholder: '请输入内容...',
    MENU_CONF: {}
}

onBeforeUnmount(() => {
    const editor = editorRef.value
    if (editor == null) return
    editor.destroy()
})

const handleCreated = (editor: any) => {
    editorRef.value = editor
}

const handleChange = (editor: any) => {
    const html = editor.getHtml()
    emit('update:content', html)
}
</script>

<style scoped>
.article-editor {
    background-color: #fff;
}
</style>
