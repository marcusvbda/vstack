<template>
    <tree-view-item :label="label" @opened="loadItems" :default_visible="default_visible" :resource="resource">
        <template v-if="loading_items">
            <div class="tree-view-item py-0" v-for="i in $randomInt(3, 5)" :key="i">
                <div class="tree-view-label">
                    <div class="shimmer resource-tree-item" :style="{ width: `${$randomInt(10, 50)}%` }" />
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
                <a href="#" class="btn-link">
                    <i class="el-icon-plus mr-1" />
                    Selecionar/Cadastrar {{ singular_label }}
                </a>
            </div>
            <div class="tree-view-item py-0" v-for="(item, i) in items" :key="`_${i}`">
                <ul class="tree-view-label hoverable item">
                    <li>
                        <v-runtime-template
                            v-if="label_index && !template"
                            :key="i"
                            :template="`<span>${item[label_index]}</span>`"
                        />
                        <v-runtime-template v-else-if="template" :key="i" :template="template" />
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
        };
    },
    created() {
        if (this.visible && !this.loaded) {
            this.loadItems();
        }
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
