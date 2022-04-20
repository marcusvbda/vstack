<template>
    <tree-view-item
        :label="label"
        @opened="loadItems"
        :default_visible="default_visible"
        :resource="resource"
        :singular_label="singular_label"
        @filter-changed="filterChanged"
    >
        <template v-if="loading_items">
            <div class="tree-view-item py-0" v-for="(i, ix) in $randomInt(3, 5)" :key="i">
                <div class="tree-view-label">
                    <div class="shimmer resource-tree-item" :style="{ width: `${$randomInt(10, 50)}%` }" />
                    <div class="ml-auto w-25" v-if="ix == 0">
                        <div class="shimmer resource-tree-item" :style="{ width: '100%' }" />
                    </div>
                </div>
            </div>
            <div class="tree-view-item py-0">
                <div class="tree-view-label">
                    <div class="ml-auto w-25">
                        <div class="shimmer resource-tree-item" :style="{ width: '100%' }" />
                    </div>
                </div>
            </div>
        </template>
        <template v-else>
            <div class="tree-view-label" v-if="!disabled">
                <a href="#" class="btn-link">
                    <i class="el-icon-plus mr-1" />
                    Selecionar {{ label }}
                </a>
            </div>
            <template v-if="items.data.length">
                <div class="tree-view-item py-0" v-for="(item, i) in items.data" :key="`_${i}`">
                    <ul class="tree-view-label hoverable item striped-list">
                        <li class="w-100">
                            <div class="d-flex align-items-center w-100 no-margin">
                                <v-runtime-template
                                    v-if="template"
                                    :key="i"
                                    :template="`<div>${template}</div>`"
                                    :templateProps="{ item }"
                                />
                                <template v-else>
                                    <span class="mr-4">
                                        <v-runtime-template
                                            v-if="template_code"
                                            :key="i"
                                            :template="`<div>${template_code}</div>`"
                                            :templateProps="{ item }"
                                        />
                                        <b class="text-muted" v-else>#{{ item.code }}</b>
                                    </span>
                                    <v-runtime-template :key="i" :template="`<span>${item[label_index]}</span>`" />
                                </template>
                                <el-tooltip class="item" effect="dark" content="Ver em detalhes" placement="top" v-if="!disabled">
                                    <el-button
                                        class="show-on-hover ml-auto"
                                        plain
                                        size="mini"
                                        type="success"
                                        icon="el-icon-search"
                                    />
                                </el-tooltip>
                            </div>
                        </li>
                    </ul>
                    <div class="my-3" v-for="(child, i) in children" :key="i">
                        <tree-view
                            :label="child.label"
                            :singular_label="child.singular_label"
                            :children="child.children"
                            :route_load="route_load"
                            :input="child"
                            :parent_id="item.id"
                            :default_visible="false"
                            :resource="child.resource"
                            :parent_resource="child.parent_resource"
                            :template_code="child.template_code"
                            :label_index="child.label_index ? child.label_index : 'name'"
                            :template="child.template"
                            class="ml-5 mb-4 mr-2"
                        />
                    </div>
                </div>
                <div class="my-3 d-flex align-items-center justify-content-end" v-if="items.last_page > 1">
                    <el-pagination
                        v-if="parent_id"
                        @current-change="handleCurrentChange"
                        small
                        background
                        layout="prev, pager, next"
                        :total="items.total"
                        :page-size="items.per_page"
                        :current-page="current_page"
                    />
                </div>
            </template>
            <template v-else>
                <div class="my-4 d-flex align-items-center justify-content-center">
                    <small class="text-muted">
                        {{ filter ? "Nada encontrado, revise seu filtro ..." : `Nada relacionado ...` }}
                    </small>
                </div>
            </template>
        </template>
    </tree-view-item>
</template>
<script>
import VRuntimeTemplate from "v-runtime-template";

export default {
    props: [
        "label",
        "children",
        "singular_label",
        "parent_resource",
        "input",
        "route_load",
        "parent_id",
        "default_visible",
        "label_index",
        "template",
        "disabled",
        "resource",
        "template_code",
    ],
    components: {
        "v-runtime-template": VRuntimeTemplate,
        "tree-view-item": require("./-TreeViewItem.vue").default,
    },
    data() {
        return {
            loaded: false,
            items: {
                data: [],
            },
            loading_items: true,
            current_page: 1,
            filter: "",
        };
    },
    created() {
        if (this.visible && !this.loaded) {
            this.loadItems();
        }
    },
    methods: {
        filterChanged(val) {
            this.filter = val;
            this.loadItems(true);
        },
        handleCurrentChange(page) {
            this.current_page = page;
            this.loadItems(true);
        },
        loadItems(ignore_loaded = false) {
            if (!ignore_loaded && this.loaded) {
                return;
            }
            this.loading_items = true;
            if (!this.parent_id) {
                this.loaded = true;
                this.loading_items = false;
                return;
            }
            const dataset = { parent_id: this.parent_id, ...this.input, filter: this.filter, page: this.current_page };
            this.$http.post(`${this.route_load}/load-items`, dataset).then(({ data }) => {
                this.loaded = true;
                this.items = data;
                this.loading_items = false;
            });
        },
    },
};
</script>
