<template>
    <div :class="['tree-view-item', parent_resource]">
        <div :class="['tree-view-label hoverable gray', visible && 'opened']" @click="visible = !visible">
            <i :class="['icon', visible ? 'el-icon-caret-bottom' : 'icon el-icon-caret-right']" />
            {{ label }} - {{ parent_resource }}
        </div>
        <div class="tree-view-children" v-show="visible"><slot /></div>
    </div>
</template>
<script>
export default {
    props: ["label", "default_visible", "parent_resource"],
    data() {
        return {
            visible: this.default_visible ? true : false,
            loading: true,
        };
    },
    watch: {
        visible(val) {
            if (val) {
                this.$emit("opened");
            }
        },
    },
    created() {
        if (this.default_visible) {
            this.$emit("opened");
        }
    },
};
</script>
