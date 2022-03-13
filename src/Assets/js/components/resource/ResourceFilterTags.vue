<template>
    <div class="d-flex align-items-center flex-row flex-wrap">
        <ElTag
            class="filter-tag mb-2 ml-0 mr-2"
            size="mini"
            :closable="prevent_close == undefined"
            v-for="(f, i) in selected_filters"
            :key="i"
            effect="dark"
            @close="handleClose(f.index)"
            style="height: auto"
        >
            <b class="mr-2">{{ f.label }}</b>
            : <span class="ml-2" v-html="f.content" />
        </ElTag>
    </div>
</template>
<script>
export default {
    props: ["get_params", "resource_filters", "prevent_close"],
    computed: {
        selected_filters() {
            return this.resource_filters
                .map((rf) => {
                    if (this.hasContent(this.get_params, rf.index)) {
                        let content = "";
                        if (rf.component == "select-filter") {
                            if (!rf?.multiple) {
                                content = rf.options.find((x) => x.value == this.get_params[rf.index]).label;
                            } else {
                                let ids = this.get_params[rf.index]
                                    .split(",")
                                    .map((x) => (x ? (!isNaN(Number(x)) ? Number(x) : x) : ""));
                                content = rf.options
                                    .filter((x) => ids.includes(x.value))
                                    .map((x) => x.label)
                                    .filter((x) => x)
                                    .join(", <br>");
                            }
                        } else {
                            content = this.get_params[rf.index];
                        }
                        return {
                            label: rf.label,
                            index: rf.index,
                            content: content,
                        };
                    }
                })
                .filter((x) => x);
        },
    },
    methods: {
        hasContent(filter, key) {
            if (!filter) return false;
            if (filter[key]) {
                if (Array.isArray(filter[key])) {
                    return filter[key].filter((x) => x).length > 0 ? true : false;
                }
                return true;
            }
            return false;
        },
        handleClose(index) {
            let params = Object.assign({}, this.get_params);
            delete params[index];
            if (params["page"]) delete params["page"];
            let query = Object.keys(params)
                .map((k) => `${encodeURIComponent(k)}=${encodeURIComponent(params[k])}`)
                .join("&");
            this.$loading({ text: "Atualizando Filtros..." });
            return (window.location.href = `${window.location.href.split("?")[0]}?${query}`);
        },
    },
};
</script>
<style lang="scss">
.filter-tag {
    .cut-text {
        text-overflow: ellipsis;
        overflow: hidden;
        max-width: 200px;
        white-space: nowrap;
    }
    &.filter-tag.el-tag.el-tag--mini.el-tag--dark {
        display: flex;
        align-items: center;
    }
}
</style>
