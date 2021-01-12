<template>
    <tbody>
        <tr v-if="f.label" class="tr-hover">
            <td class="px-2 tr-label" style="position: relative" @click="visible = !visible">
                <span class="badge-number" v-if="index != 'per_page' && $root.$refs.tags_filter.hasContent(filter, index)" v-html="'!'" />
                <div class="w-100">
                    <div class="d-flex flex-row justify-content-between px-3 font-weight-bold">
                        <label class="mb-0 text-muted" style="font-size: 13px; font-weight: bold" v-html="f.label ? f.label : k" />
                        <span :class="`el-icon-arrow-${!visible ? 'down' : 'up'}`" />
                    </div>
                </div>
            </td>
        </tr>
        <tr v-if="visible">
            <td class="px-2">
                <v-runtime-template :key="k" :template="f.view" />
            </td>
        </tr>
    </tbody>
</template>
<script>
import VRuntimeTemplate from 'v-runtime-template'
export default {
    props: ['filter', 'k', 'data', 'f', 'index', 'makeNewRoute'],
    data() {
        return {
            visible: this.$root.$refs.tags_filter.hasContent(this.filter, this.index) ? true : false,
            timer: null,
        }
    },
    components: {
        'v-runtime-template': VRuntimeTemplate,
    },
    methods: {
        showConfirm() {
            this.$emit('show-confirm-btn', true)
        },
    },
}
</script>
<style scoped lang="scss">
.tr-hover {
    opacity: 0.7;
    transition: 0.4s;
    cursor: pointer;
    &:hover {
        opacity: 1;
        background-color: #ececec;
    }
}
</style>
