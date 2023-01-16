<template>
    <div>
        <a @click.prevent="visible = !visible" href="#">{{ decodedText }}</a>
        <el-dialog :title="title" :visible.sync="visible" :width="width">
            <div style="word-break: break-word;">
                <slot />
            </div>
        </el-dialog>
    </div>
</template>
<script>
export default {
    props: {
        text: {
            type: String,
            default: ""
        },
        title: {
            type: String,
            default: ""
        },
        width: {
            type: String,
            default: "50%"
        }
    },
    data() {
        return {
            visible: false
        }
    },
    computed: {
        decodedText() {
            return decodeURIComponent(escape(this.text));
        }
    }
}
</script>