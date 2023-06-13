<template>
    <div class="flex items-center flex-wrap" style="gap:5px'">
        <ElTag
            class="filter-tag mb-2"
            size="mini"
            :closable="prevent_close == undefined"
            v-for="(f, i) in selected_filters"
            :key="i"
            effect="dark"
            @close="handleClose(f.index)"
            style="height: auto"
            :id="`resource-filter-tag-${f.index}`"
        >
            <b class="mr-2">{{ f.label }}</b> :
            <ResourceTagContent :filter="f" />
        </ElTag>
    </div>
</template>
<script>
import ResourceTagContent from './partials/-resource-tag-content.vue';
export default {
    props: ['get_params', 'resource_filters', 'prevent_close'],
    components: {
        ResourceTagContent,
    },
    computed: {
        selected_filters() {
            const results = this.resource_filters
                .map((rf) => {
                    if (this.hasContent(this.get_params, rf.index)) {
                        let content = '';
                        if (
                            rf.component == 'select-filter' &&
                            rf.options &&
                            rf.options.length
                        ) {
                            if (!rf?.multiple) {
                                content =
                                    rf.options.find(
                                        (x) =>
                                            x.value == this.get_params[rf.index]
                                    )?.label ?? '';
                            } else {
                                let ids = this.get_params[rf.index]
                                    .split(',')
                                    .map((x) =>
                                        x
                                            ? !isNaN(Number(x))
                                                ? Number(x)
                                                : x
                                            : ''
                                    );
                                content = rf.options
                                    .filter(
                                        (x) =>
                                            ids.includes(x.value) ||
                                            ids.includes(parseInt(x.value))
                                    )
                                    .map((x) => x.label)
                                    .filter((x) => x)
                                    .join(', <br>');
                            }
                        } else {
                            content = this.processWithOptions(rf);
                        }
                        return {
                            label: rf.label,
                            index: rf.index,
                            content: content,
                            get_value: this.get_params[rf.index] ?? '',
                            original: rf,
                        };
                    }
                })
                .filter((x) => x);
            return results;
        },
    },
    methods: {
        processWithOptions(rf) {
            const value = this.get_params[rf.index];
            if (rf._options && rf._options[value]) {
                return rf._options[value][0];
            }
            return value;
        },
        hasContent(filter, key) {
            if (!filter) return false;
            if (filter[key]) {
                if (Array.isArray(filter[key])) {
                    return filter[key].filter((x) => x).length > 0
                        ? true
                        : false;
                }
                return true;
            }
            return false;
        },
        handleClose(index) {
            let params = Object.assign({}, this.get_params);
            delete params[index];
            if (params['page']) delete params['page'];
            let query = Object.keys(params)
                .map(
                    (k) =>
                        `${encodeURIComponent(k)}=${encodeURIComponent(
                            params[k]
                        )}`
                )
                .join('&');
            this.$loading({ text: 'Atualizando Filtros...' });
            return (window.location.href = `${
                window.location.href.split('?')[0]
            }?${query}`);
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
