<template>
    <ElDialog :title="title" :visible.sync="visible" :width="size" :close-on-press-escape="false"
        class="dialog-has-many" :close-on-click-modal="false" :append-to-body="true">
        <div class="shimmer" :style="{height : 512}" v-if="loading" />
        <template v-else>
            <el-table :data="items" stripe style="width: 100%">
                <el-table-column v-for="key in Object.keys(table)" align="left" :key="key" :prop="key"
                    :label="table[key].label" />
                <el-table-column align="right" width="400">
                    <template slot="header" slot-scope="scope">
                        <div class="d-flex align-items-center">
                            <slot name="actions" />
                            <el-input v-model="filter" size="mini" :placeholder="`Encontrar ${label}`"
                                :clearable="true" />
                        </div>
                    </template>
                    <template slot-scope="scope">
                        <el-button size="mini" :disabled="ignore_ids.includes(scope.row.id)" @click="select(scope.row)">
                            Selecionar
                        </el-button>
                    </template>
                </el-table-column>
            </el-table>
            <div class="d-flex py-4 align-items-center justify-content-center">
                <el-pagination background layout="prev, pager, next" :current-page="current_page" :total="total"
                    :page-size="per_page" @current-change="pageChanged" />
            </div>
        </template>
    </ElDialog>
</template>
<script>
export default {
    props: {
        size: {
            type: String,
            default: "75%"
        },
        table: {
            type: Object,
            default: () => ({
                name: {
                    label: "Nome"
                }
            })
        },
        ignore_ids: {
            type: Array,
            default: () => ([])
        },
        resource: String,
        label: String,
        singular_label: String,
        resource_id: String
    },
    data() {
        return {
            dialogCreate: false,
            loading: true,
            items: [],
            filter: "",
            current_page: 1,
            per_page: 1,
            total: 0,
            visible: false,
            timeout: null
        }
    },
    watch: {
        visible(val) {
            if (val) {
                this.initDialog()
            }
            this.dialogVisible = val
        },
        filter(val) {
            clearTimeout(this.timeout)
            this.timeout = setTimeout(() => {
                this.getData(1, val)
            }, 1000)
        }
    },
    computed: {
        title() {
            return `Seleção de ${this.singular_label}`
        }
    },
    methods: {
        select(row) {
            this.setVisible(false)
            this.$emit("on-selected", row)
        },
        pageChanged(page) {
            this.getData(page)
        },
        setVisible(state) {
            this.visible = state
        },
        initDialog() {
            this.items = ""
            this.filter = ""
            this.current_page = 1
            this.last_page = 1

            this.getData(1)
        },
        getData(page, filter = "") {
            this.loading = true
            const payload = {
                params: { page, _: filter }
            }
            this.$http
                .get(`/admin/${this.resource_id}/select-list`, payload)
                .then(({ data }) => {
                    this.items = data.list.data
                    this.total = data.list.total
                    this.per_page = data.list.per_page
                    this.current_page = data.list.current_page
                    this.loading = false
                })
                .catch((er) => {
                    this.loading = false;
                    console.log(er);
                });
        }
    }
}
</script>