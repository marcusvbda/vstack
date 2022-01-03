<template>
    <div>
        <template v-if="virtual_columns.includes(col)">
            <template v-if="col == virtual_indexes.after_row">
                <slot name="after_row" :after_row="after_row" :after_row_content="row.content.after_row" />
            </template>
            <template v-if="col == virtual_indexes.action_col">
                <div class="d-flex align-items-center justify-content-center">
                    <input
                        class="select-action-resource select_action_box"
                        type="checkbox"
                        :id="`${resource_id}_action_select_${row.content.id}`"
                    />
                </div>
            </template>
            <template v-if="col == virtual_indexes.right_actions_column">
                <resource-crud-buttons
                    :resource_id="resource_id"
                    :data="{
                        code: row.content.code,
                        route: `${resource_route}/${row.content.code}`,
                        can_view: row.acl.can_view,
                        can_update: row.acl.can_update,
                        can_delete: row.acl.can_delete,
                        can_clone: row.acl.can_clone,
                        before_delete: row.acl.before_delete,
                        crud_type: row.acl.crud_type,
                        resource_label: row.acl.resource_label,
                        resource_icon: row.acl.resource_icon,
                        resource_singular_label: row.acl.resource_singular_label,
                    }"
                    :id="row.content.id"
                />
            </template>
        </template>
        <template v-else>
            <template v-if="first_key">
                <b>
                    <template v-if="row.acl.can_view || row.acl.can_update">
                        <template v-if="row.acl.can_view">
                            <a :href="`${resource_route}/${row.content.code}`">
                                <v-runtime-template :template="`<div>${row.content[col]}</div>`" />
                            </a>
                        </template>
                        <template v-else>
                            <a :href="`${resource_route}/${row.content.code}/edit`">
                                <v-runtime-template :template="`<div>${row.content[col]}</div>`" />
                            </a>
                        </template>
                    </template>
                    <template v-else>
                        <v-runtime-template class="text-muted" :template="`<div>${row.content[col]}</div>`" />
                    </template>
                </b>
            </template>
            <template v-else>
                <v-runtime-template :template="`<div>${row.content[col]}</div>`" />
            </template>
        </template>
    </div>
</template>
<script>
import VRuntimeTemplate from "v-runtime-template";

export default {
    props: ["col", "first_key", "row", "resource_id", "virtual_indexes", "resource_route"],
    components: {
        "v-runtime-template": VRuntimeTemplate,
    },
    data() {
        return {
            after_row: {
                visible: false,
            },
        };
    },
    computed: {
        virtual_columns() {
            return Object.keys(this.virtual_indexes).map((key) => {
                return this.virtual_indexes[key];
            });
        },
    },
};
</script>
