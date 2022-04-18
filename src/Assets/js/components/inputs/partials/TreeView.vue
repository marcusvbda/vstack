<template>
    <tree-view-item :label="label" @opened="loadItems" :default_visible="default_visible" :resource="resource">
        <template v-if="loading_items">
            <div class="tree-view-item py-0" v-for="(i, ix) in $randomInt(3, 5)" :key="i">
                <div class="tree-view-label">
                    <div class="shimmer resource-tree-item" :style="{ width: `${$randomInt(10, 50)}%` }" />
                    <div class="ml-auto w-25" v-if="ix == 0">
                        <div class="shimmer resource-tree-item" :style="{ width: '100%' }" />
                    </div>
                </div>
            </div>
            <div class="tree-view-item ml-5 mb-4 mr-2" v-for="i in $randomInt(1, 2)" :key="`secondary_${i}`">
                <div class="tree-view-label">
                    <div class="shimmer resource-tree-item" :style="{ width: `${$randomInt(10, 50)}%` }" />
                </div>
            </div>
        </template>
        <template v-else>
            <div class="tree-view-label">
                <a href="#" class="btn-link" v-if="!disabled">
                    <i class="el-icon-plus mr-1" />
                    Selecionar {{ label }}
                </a>
                <div class="ml-auto w-25">
                    <el-input
                        clearable
                        :placeholder="`Encontrar ${singular_label} ...`"
                        prefix-icon="el-icon-search"
                        v-model="filter"
                        size="mini"
                    />
                </div>
            </div>
            <template v-if="filtered_items.length">
                <div class="tree-view-item py-0" v-for="(item, i) in filtered_items" :key="`_${i}`">
                    <ul class="tree-view-label hoverable item striped-list">
                        <li class="w-100">
                            <div class="d-flex align-items-center w-100">
                                <v-runtime-template
                                    v-if="label_index && !template"
                                    :key="i"
                                    :template="`<span>${item[label_index]}</span>`"
                                />
                                <v-runtime-template
                                    v-else-if="template"
                                    :key="i"
                                    :template="template"
                                    :templateProps="{ item }"
                                />
                                <el-tooltip class="item" effect="dark" content="Ver em detalhes" placement="top">
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
                    <div class="mt-3" v-for="(child, i) in children" :key="i">
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
                            :label_index="child.label_index ? child.label_index : 'name'"
                            :template="child.template"
                            class="ml-5 mb-4 mr-2"
                        />
                    </div>
                </div>
            </template>
            <template v-else-if="filter">
                <div class="my-4 d-flex align-items-center justify-content-center">
                    <small class="text-muted">Nada encontrado ...</small>
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
    ],
    components: {
        "v-runtime-template": VRuntimeTemplate,
        "tree-view-item": require("./-TreeViewItem.vue").default,
    },
    data() {
        return {
            loaded: false,
            items: [],
            loading_items: true,
            filter: "",
        };
    },
    created() {
        if (this.visible && !this.loaded) {
            this.loadItems();
        }
    },
    computed: {
        filtered_items() {
            return this.items.filter((item) => {
                return item[this.label_index].toLowerCase().indexOf(this.filter.toLowerCase()) !== -1;
            });
        },
    },
    methods: {
        loadItems(ignore_loaded = false) {
            if (!ignore_loaded && this.loaded) {
                return;
            }
            if (!this.parent_id) {
                this.loaded = true;
                this.loading_items = false;
                return (this.items = []);
            }
            this.loading_items = true;
            const dataset = { parent_id: this.parent_id, ...this.input };
            this.$http.post(`${this.route_load}/load-items`, dataset).then(({ data }) => {
                this.loaded = true;
                this.items = data;
                this.loading_items = false;
            });
        },
    },
};
</script>
