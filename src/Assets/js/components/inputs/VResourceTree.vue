<template>
    <tr>
        <td colspan="2">
            <template v-if="loading">
                <div class="shimmer resource-tree" />
            </template>
            <template v-else>
                <div class="tree-view">
                    <template v-for="(resource, i) in tree">
                        <tree-view
                            :key="i"
                            :label="resource.label"
                            :parent_resource="resource.parent_resource"
                            :singular_label="resource.singular_label"
                            :children="resource.children"
                            :route_load="route_load"
                            :input="resource"
                            :parent_id="form.id"
                            :default_visible="true"
                        />
                    </template>
                </div>
            </template>
        </td>
    </tr>
</template>
<script>
export default {
    props: ["resource", "disabled", "route_load", "form", "relation", "parent_resource"],
    data() {
        return {
            loading: true,
            tree: [],
        };
    },
    created() {
        setTimeout(() => {
            if (!this._isDestroyed) {
                this.loadComponent();
            }
        });
    },
    methods: {
        loadComponent() {
            this.$http
                .post(this.route_load, {
                    resource: this.resource,
                    parent_resource: this.parent_resource,
                })
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
        cursor: pointer;
        border-radius: 5px;

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

            &.item {
                font-weight: 400;
            }

            .icon {
                margin-right: 5px;
            }

            &.gray {
                background-color: #ededed;
            }

            &.hoverable {
                &:hover,
                &.opened {
                    background-color: #ededed;
                    transition: 0.4s;
                }
            }
        }
    }
}
</style>
