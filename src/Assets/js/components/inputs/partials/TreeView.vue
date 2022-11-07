<template>
    <TreeViewItem :label="label" @opened="loadItems" :default_visible="default_visible" :resource="resource"
        :singular_label="singular_label" @filter-changed="filterChanged">
        <ElDialog custom-class="tree-view-details" :visible.sync="show_detail" width="60%" top="20px"
            :close-on-click-modal="false" :close-on-press-escape="false" :destroy-on-close="true">
            <TreeViewDialogCrud v-if="show_detail" :resource="input.resource" :selected="selected"
                :label="input.singular_label" @close="show_detail = false" :qtyfields="input.qty_fields"
                @saved="savedDetail" :fk_index="input.foreign_key" :fk_value="parent_id" :acl="input.acl" />
        </ElDialog>

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
                <a href="#" class="btn-link" @click.prevent="showDetail({})">
                    <i class="el-icon-plus mr-1" />
                    Adicionar {{ label }}
                </a>
            </div>
            <template v-if="items.length">
                <div class="tree-view-item py-0" v-for="(item, i) in items" :key="`_${i}`">
                    <ul class="tree-view-label hoverable item striped-list">
                        <li class="w-100">
                            <div class="d-flex align-items-center w-100 no-margin">
                                <VRuntimeTemplate v-if="template" :key="i" :template="`<div>${template}</div>`"
                                    :templateProps="{ item, counter: i + 1 }" />
                                <template v-else>
                                    <VRuntimeTemplate :key="i"
                                        :template="`<span class='word-break-all mr-3'>${item[label_index]}</span>`" />
                                </template>
                                <ElTooltip class="item" effect="dark" content="Ver em detalhes" placement="right"
                                    v-if="!disabled">
                                    <ElButton v-if="input.acl.delete || input.acl.update" class="show-on-hover ml-auto"
                                        plain size="mini" type="success" icon="el-icon-search"
                                        @click="showDetail(item)" />
                                </ElTooltip>
                            </div>
                        </li>
                    </ul>
                    <div class="my-3" v-for="(child, i) in children" :key="i">
                        <TreeView :label="child.label" :singular_label="child.singular_label" :children="child.children"
                            :route_load="route_load" :input="child" :parent_id="item.id" :default_visible="false"
                            :resource="child.resource" :parent_resource="child.parent_resource"
                            :template_code="child.template_code"
                            :label_index="child.label_index ? child.label_index : 'name'" :template="child.template"
                            class="ml-5 mb-2 mr-2" />
                    </div>
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
    </TreeViewItem>
</template>
<script>
import VRuntimeTemplate from "v-runtime-template";
import TreeViewItem from "./-TreeViewItem.vue";
import TreeViewDialogCrud from "./-TreeViewDialogCrud.vue";

const initialState = () => ({
    loaded: false,
    items: [],
    loading_items: true,
    current_page: 1,
    filter: "",
    show_detail: false,
    selected: null,
});

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
        VRuntimeTemplate,
        TreeViewItem,
        TreeViewDialogCrud,
    },
    data() {
        return initialState();
    },
    created() {
        if (this.visible && !this.loaded) {
            this.loadItems();
        }
    },
    methods: {
        savedDetail(message) {
            this.$message({ showClose: true, message, type: "success" });
            this.show_detail = false;
            Object.assign(this.$data, initialState());
            this.loadItems();
        },
        showDetail(item) {
            this.show_detail = true;
            this.selected = item;
        },
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
            const payload = {
                params: dataset
            }
            this.$http.get(`${this.route_load}/load-items`, payload).then(({ data }) => {
                this.loaded = true;
                this.items = data;
                this.loading_items = false;
            });
        },
    },
};
</script>
