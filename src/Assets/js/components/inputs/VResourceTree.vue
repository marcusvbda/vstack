<template>
    <tr>
        <td colspan="2">
            <template v-if="loading">
                <div class="shimmer resource-tree" />
            </template>
            <template v-else>
                <div class="tree-view">
                    <template v-for="(resource, i) in tree">
                        <TreeView :disabled="disabled" :key="i" :label="resource.label"
                            :parent_resource="resource.parent_resource" :resource="resource.resource"
                            :singular_label="resource.singular_label" :children="resource.children"
                            :route_load="route_load" :input="resource"
                            :label_index="resource.label_index ? resource.label_index : 'name'"
                            :template="resource.template" :parent_id="form.id" :template_code="resource.template_code"
                            :default_visible="true" />
                    </template>
                </div>
            </template>
        </td>
    </tr>
</template>
<script>
export default {
    props: ["resource", "disabled", "form", "relation", "parent_resource"],
    data() {
        return {
            loading: true,
            tree: [],
            route_load: "/admin/inputs/resource-tree",
        };
    },
    created() {
        this.loadComponent();
    },
    methods: {
        loadComponent() {
            const payload = {
                params: {
                    resource: this.resource,
                    parent_resource: this.parent_resource,
                }
            }
            this.$http
                .get(this.route_load, payload)
                .then(({ data }) => {
                    this.tree = data;
                    this.loading = false;
                });
        },
    },
};
</script>

<style lang="scss">
.shimmer {
    &.resource-tree {
        height: 350px;
        width: 100%;
        border-radius: 5px;
    }

    &.resource-tree-item {
        height: 24px;
    }
}

.tree-view {
    display: flex;
    flex-direction: column;

    .tree-view-item {
        &.group {
            border: 1px solid #ededed;
            border-radius: 5px;
        }

        cursor: pointer;

        .btn-link {
            font-weight: 600;
            font-size: 12px;
        }

        .tree-view-label {
            padding: 5px 10px;
            font-weight: bold;
            font-size: 13px;
            color: #444;
            display: flex;
            align-items: center;
            min-height: 39px;

            .no-margin {
                p {
                    margin-bottom: 0 !important;
                }
            }

            &.item {
                font-weight: 400;
                padding-left: 31px;
                margin-bottom: 0;
            }

            .icon {
                margin-right: 5px;
            }

            .show-on-hover {
                visibility: hidden;
            }

            &.gray {
                background-color: #ededed;
            }

            &.hoverable {

                &:hover,
                &.opened {
                    background-color: #ededed;
                    transition: 0.4s;

                    &.item {
                        background-color: #ecf0ff;
                    }

                    .show-on-hover {
                        transition: 0.4s;
                        visibility: visible;
                    }
                }
            }
        }
    }
}
</style>
