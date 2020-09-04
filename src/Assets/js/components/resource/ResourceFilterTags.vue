<template>
    <div class="d-flex align-items-center flex-row">
        <el-tag
            class="filter-tag"
            size="mini"
            closable
            v-for="(f,i) in selected_filters"
            :key="i"
            effect="dark"
            @close="handleClose(f.index)"
            :title="`${f.content}`"
        >
            <div class="cut-text">
                <b>{{f.label}}</b>
                : {{f.content}}
            </div>
        </el-tag>
    </div>
</template>
<script>
export default {
    props: ['get_params', 'resource_filters'],
    computed: {
        selected_filters() {
            return this.resource_filters.map(rf => {
                if (this.hasContent(this.get_params, rf.index)) return { label: rf.label, index: rf.index, content: this.get_params[rf.index] }
            }).filter(x => x)
        }
    },
    methods: {
        hasContent(filter, key) {
            if (filter[key]) {
                if (Array.isArray(filter[key])) { return filter[key].length > 1 ? true : null }
                return true
            }
            return false
        },
        handleClose(index) {
            let params = Object.assign({}, this.get_params)
            delete params[index]
            let query = Object.keys(params).map(k => `${encodeURIComponent(k)}=${encodeURIComponent(params[k])}`).join('&')
            this.$loading({ text: 'Atualizando Filtros...' })
            return window.location.href = `${window.location.href.split('?')[0]}?${query}`
        }
    }
}
</script>
<style lang="scss" >
.filter-tag {
    .cut-text {
        text-overflow: ellipsis;
        overflow: hidden;
        width: 150px;
        white-space: nowrap;
    }
    &.filter-tag.el-tag.el-tag--mini.el-tag--dark {
        display: flex;
        align-items: center;
    }
}
</style>