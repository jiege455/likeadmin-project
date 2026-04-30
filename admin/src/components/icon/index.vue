<script lang="ts">
/**
 * 开发者公众号：杰哥网络科技
 * qq2711793818 杰哥
 */
import { ElIcon } from 'element-plus'
import { createVNode, defineComponent, h, resolveComponent, shallowRef } from 'vue'

import { EL_ICON_PREFIX, LOCAL_ICON_PREFIX } from './index'
import svgIcon from './svg-icon.vue'

const iconCache = new Map<string, any>()

export default defineComponent({
    name: 'Icon',
    props: {
        name: {
            type: String,
            required: true
        },
        size: {
            type: [String, Number],
            default: '14px'
        },
        color: {
            type: String,
            default: 'inherit'
        }
    },
    setup(props) {
        return () => {
            if (props.name.indexOf(EL_ICON_PREFIX) === 0) {
                const iconName = props.name.replace(EL_ICON_PREFIX, '')
                let cachedIcon = iconCache.get(iconName)
                if (!cachedIcon) {
                    try {
                        cachedIcon = shallowRef(resolveComponent(iconName))
                        iconCache.set(iconName, cachedIcon)
                    } catch (e) {
                        return null
                    }
                }
                return createVNode(
                    ElIcon,
                    {
                        size: props.size,
                        color: props.color
                    },
                    {
                        default: () => createVNode(cachedIcon.value)
                    }
                )
            }
            if (props.name.indexOf(LOCAL_ICON_PREFIX) === 0) {
                return h(
                    'i',
                    {
                        class: ['local-icon']
                    },
                    createVNode(svgIcon, { ...props })
                )
            }
            return null
        }
    }
})
</script>
