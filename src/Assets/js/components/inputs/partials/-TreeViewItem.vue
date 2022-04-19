<template>
    <div :class="['tree-view-item group', resource]">
        <div :class="['tree-view-label hoverable gray', visible && 'opened']">
            <div class="w-75" @click="visible = !visible">
                <i :class="['icon', visible ? 'el-icon-caret-bottom' : 'icon el-icon-caret-right']" />
                {{ label }}
            </div>
            <div class="ml-auto w-25">
                <el-input
                    clearable
                    :placeholder="`Encontrar  ${singular_label} ...`"
                    prefix-icon="el-icon-search"
                    v-model="filter"
                    size="mini"
                />
            </div>
        </div>
        <div class="tree-view-children" v-if="visible"><slot /></div>
    </div>
</template>
<script>
export default {
    props: ["label", "default_visible", "resource", "singular_label"],
    data() {
        return {
            visible: this.default_visible ? true : false,
            loading: true,
            filter: "",
            interval: null,
        };
    },
    watch: {
        visible(val) {
            if (val) {
                this.$emit("opened");
            }
        },
        filter(val) {
            clearInterval(this.interval);
            this.interval = setInterval(() => {
                this.$emit("filter-changed", val);
                clearInterval(this.interval);
            }, 400);
        },
    },
    created() {
        if (this.default_visible) {
            this.$emit("opened");
        }
    },
};
</script>
