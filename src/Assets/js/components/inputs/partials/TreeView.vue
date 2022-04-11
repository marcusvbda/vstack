<template>
    <tree-view-item :label="label" @opened="loadItems" :default_visible="default_visible" :parent_resource="parent_resource">
        <template v-if="loading_items">
            <div class="tree-view-item py-0" v-for="i in 5" :key="i">
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
                <div class="tree-view-label hoverable item">
                    {{ item.label }}
                </div>
                <div class="mt-3" v-for="(child, i) in children" :key="i">
                    <tree-view
                        :label="child.label"
                        :singular_label="child.singular_label"
                        :children="child.children"
                        :route_load="route_load"
                        :input="child"
                        :parent_id="item.id"
                        :default_visible="false"
                        :parent_resource="child.parent_resource"
                        class="pl-4"
                    />
                </div>
            </div>
        </template>
    </tree-view-item>
</template>
<script>
export default {
    props: ["label", "children", "singular_label", "parent_resource", "input", "route_load", "parent_id", "default_visible"],
    data() {
        return {
            loaded: false,
            items: [],
            loading_items: true,
        };
    },
    watch: {
        visible(val) {
            if (val && !this.loaded) {
                this.loadItems();
            }
        },
    },
    created() {
        if (this.visible && !this.loaded) {
            this.loadItems();
        }
    },
    methods: {
        loadItems(ignore_loaded = false) {
            if (ignore_loaded && !this.loaded) {
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
