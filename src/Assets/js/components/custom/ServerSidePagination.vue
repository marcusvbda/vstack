<template>
    <div :class="custom_class">
        <div :class="class_header">
            <div :class="class_header_slot">
                <slot name="header" />
            </div>
            <div :class="class_filter_section">
                <el-input :placeholder="filter_placeholder" :class="class_filter_input" v-model="filter"
                    @keyup.native="changeFilter" @change="changeFilter(false)" :clearable="true">
                    <i slot="prefix" class="el-input__icon el-icon-search" />
                </el-input>
            </div>
        </div>
        <div :class="class_pagination_section" v-if="!loading && total_items > per_page">
            <el-pagination background layout="prev, pager, next" :current-page="current_page" :disabled="timeout > 0"
                :page-size="per_page" @current-change="changePage" :total="total_items" />
        </div>
        <div :class="class_content">
            <slot name="loading" v-if="loading" />
            <template v-else-if="items.length">
                <slot name="item" v-for="i in items" :row="i" />
            </template>
            <template v-else-if="!items.length">
                <div :class="class_no_result_slot">
                    <slot name="no-result-slot" />
                </div>
            </template>
        </div>
    </div>
</template>
<script>
export default {
    props: {
        fetch_route: {
            default: "/",
            type: String
        },
        class_filter_section: {
            default: "col-12 col-md-4 ml-auto d-flex align-items-end",
            type: String
        },
        class_no_result_slot: {
            default: "d-flex align-items-center justify-content-center my-5 py-5 col-12",
            type: String
        },
        class_header_slot: {
            default: "col-12 col-md-4",
            type: String
        },
        custom_class: {
            default: "col-12 pagination-component",
            type: String
        },
        class_header: {
            default: "row pagination-component-header d-flex flex-row",
            type: String
        },
        class_content: {
            default: "row pagination-component-content",
            type: String
        },
        class_pagination_section: {
            default: "w-100 d-flex justify-content-end mb-2 mt-4",
            type: String
        },
        class_filter_input: {
            default: "",
            type: String
        },
        filter_placeholder: {
            default: "Pesquisar ...",
            type: String
        },
        filter_interval: {
            default: 700,
            type: Number
        },
        per_page: {
            default: 5,
            type: Number
        },
        params: {
            default: () => ({}),
            type: Object
        }
    },
    watch: {
        params: {
            deep: true,
            handler() {
                this.changePage(1)
            }
        }
    },
    data() {
        return {
            filter: "",
            current_page: 1,
            timeout: 0,
            loading: true,
            items: [],
            total_items: 0,
        }
    },
    created() {
        this.getItems()
    },
    methods: {
        changeFilter(timeout = true) {
            clearTimeout(this.timeout)
            this.current_page = 1
            if (!timeout) {
                return this.getItems();
            }
            this.timeout = setTimeout(() => {
                this.getItems()
                this.timeout = 0
            }, this.filter_interval)
        },
        changePage(val) {
            this.current_page = val
            this.getItems()
        },
        getItems() {
            this.loading = true
            const payload = {
                page: this.current_page,
                per_page: this.per_page,
                filter: this.filter,
            }

            const paramKeys = Object.keys(this.params);
            for (let i in paramKeys) {
                payload[paramKeys[i]] = this.params[paramKeys[i]]
            }

            this.$http.get(this.fetch_route, { params: payload }).then(({ data }) => {
                this.items = data.data
                this.current_page = data.current_page
                this.total_items = data.total
                this.loading = false
            })
        }
    }
}
</script>