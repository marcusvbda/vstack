<template>
    <div
        class="dropdown"
        style="position: relative"
        v-if="data.filters.length > 0 || show_page_list"
    >
        <span
            class="badge-number out"
            v-if="qty_filters > 0"
            v-html="qty_filters"
        />
        <button
            class="vstack-btn secondary"
            type="button"
            @click.prevent="toggleFilters"
            v-html="`Filtrar ${label}`"
            id="resource-btn-filter"
        />
        <el-drawer
            :with-header="true"
            :visible.sync="drawer"
            direction="rtl"
            :before-close="confirmClose"
            :append-to-body="true"
            class="sm:w-full"
        >
            <template slot="title">
                <div class="w-1/2">
                    <button
                        class="vstack-btn primary"
                        id="resource-btn-confirm"
                        type="primary"
                        @click="makeNewRoute"
                    >
                        <span class="el-icon-search mr-2" />
                        Confirmar Filtro
                    </button>
                </div>
            </template>
            <div class="flex flex-col">
                <div class="w-full">
                    <table class="table mb-0 w-full">
                        <tbody v-if="show_page_list">
                            <tr>
                                <td
                                    class="px-2 tr-label p-4"
                                    style="position: relative"
                                >
                                    <div class="w-full">
                                        <ElSelect
                                            v-model="filter.per_page"
                                            size="medium"
                                            class="w-full"
                                            @change="showConfirmBtn = true"
                                            id="resource-filter-per_page"
                                        >
                                            <ElOption
                                                :key="op"
                                                v-for="op in per_page"
                                                :value="Number(op)"
                                                :label="`${op} ${
                                                    Number(op) > 1
                                                        ? 'resultados por página'
                                                        : 'resultado por página'
                                                }`"
                                            />
                                        </ElSelect>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        <btn-body
                            v-for="(f, key) in data.filters"
                            :key="key"
                            :k="key"
                            :f="f"
                            :filter="filter"
                            :index="f.index"
                            :data="data"
                            :makeNewRoute="makeNewRoute"
                            @show-confirm-btn="showConfirmBtn = true"
                        />
                    </table>
                </div>
            </div>
        </el-drawer>
    </div>
</template>
<script>
export default {
    props: ['data', 'label', 'per_page', 'report_mode'],
    data() {
        return {
            showConfirmBtn: false,
            drawer: false,
            route: window.location.href.split('?')[0],
            ignore_vstack_filters_index: ['_', 'context', 'page', 'key'],
            filter: {
                per_page: Number(
                    this.data?.query?.per_page
                        ? this.data?.query?.per_page
                        : Array.isArray(this.per_page)
                        ? this.per_page[0]
                        : this.per_page
                ),
            },
            timeout: null,
        };
    },
    components: {
        'btn-body': require('./partials/-filter-btn-row.vue').default,
    },
    computed: {
        qty_filters() {
            const qty = Object.keys(this.filter)
                .filter((y) => y != 'per_page')
                .map((key) => this.hasContent(this.filter, key))
                .filter((x) => x).length;
            return qty || 0;
        },
        show_page_list() {
            return Array.isArray(this.per_page);
        },
    },
    created() {
        this.initFormFilter();
    },
    mounted() {
        const el = this.$refs.content;
        if (!el) {
            return;
        }
        el.addEventListener('click', (event) => event.stopPropagation());
    },
    methods: {
        hasContent(filter, key) {
            if (!filter) {
                return false;
            }
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
        confirmClose() {
            if (this.showConfirmBtn) {
                this.$confirm(
                    'Deseja confirmar o filtro selecionado ?',
                    'Confirmação',
                    { closeOnClickModal: false }
                ).then(() => this.makeNewRoute());
            } else {
                this.closeDrawer();
            }
        },
        closeDrawer() {
            this.drawer = false;
        },
        toggleFilters() {
            this.drawer = !this.drawer;
        },
        makeNewRoute() {
            this.$loading({ text: 'Atualizando Filtros...' });
            let str_query = '';
            let filter_keys = Object.keys(this.filter).filter(
                (x) => !x.includes(['page_type'])
            );
            filter_keys.forEach(
                (key) => (this.data.query[key] = this.filter[key])
            );
            let new_data = this.data.query;
            if (new_data.page_type) {
                delete new_data.page_type;
            }
            Object.keys(this.data.query).forEach((key) => {
                if (!this.ignore_vstack_filters_index.includes(key)) {
                    if (!['null', null].includes(this.data.query[key])) {
                        if (Array.isArray(this.data.query[key])) {
                            if (this.data.query[key].length) {
                                str_query += `${key}=${this.data.query[key]}&`;
                            }
                        } else {
                            if (this.data.query[key]) {
                                str_query += `${key}=${this.data.query[key]}&`;
                            }
                        }
                    }
                }
            });
            if (this.data.query['_']) {
                str_query += `${str_query ? '&' : ''}_=${
                    this.data.query['_'] ? this.data.query['_'] : ''
                }`;
            }

            str_query = str_query.slice(0, -1);
            const route = `${this.route}${str_query ? '?' + str_query : ''}`;
            window.location.href = route;
        },
        setFormValue(index, value, filter) {
            if (filter.component == 'text-filter') value = String(value);
            if (filter.component == 'check-filter') value = value === 'true';
            if (filter.component == 'select-filter') {
                if (filter?.multiple) {
                    value = value
                        .split(',')
                        .map((x) =>
                            x ? (!isNaN(Number(x)) ? Number(x) : '') : x
                        )
                        .filter((x) => x);
                } else
                    value = value
                        ? !isNaN(Number(value))
                            ? Number(value)
                            : value
                        : '';
            }
            if (filter.component == 'rangedate-filter')
                value = value.split(',');
            this.$set(this.filter, index, value);
        },
        initFormFilter() {
            let filter_keys = Object.keys(this.data.filters);
            for (let i in filter_keys) {
                if (this.data.filters[filter_keys[i]]) {
                    if (this.data.filters[filter_keys[i]].index) {
                        this.setFormValue(
                            this.data.filters[filter_keys[i]].index,
                            this.data.query[
                                this.data.filters[filter_keys[i]].index
                            ]
                                ? this.data.query[
                                      this.data.filters[filter_keys[i]].index
                                  ]
                                : '',
                            this.data.filters[filter_keys[i]]
                        );
                    }
                }
            }
        },
    },
};
</script>
<style lang="scss">
.badge-number {
    background-color: #04a9ce;
    color: white;
    font-size: 10;
    padding: 2px;
    border-radius: 100%;
    position: absolute;
    z-index: 99;
    top: 9px;
    left: 2px;
    width: 15px;
    height: 15px !important;
    display: flex;
    align-items: center;
    justify-content: center;
    &.out {
        top: -5px;
        left: -5px;
    }
}
.el-drawer__body {
    padding-bottom: 20px;
    &:focus,
    &:active {
        outline: none !important;
    }
}
</style>
