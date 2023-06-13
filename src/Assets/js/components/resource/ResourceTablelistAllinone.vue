<template>
    <tbody class="bg-gray-50">
        <template v-for="(row, i) in rows">
            <tr :key="i" :id="`resource-body-${row.content.id}`" class="border">
                <td
                    v-for="(col, y) in columns"
                    :key="`${i}_${y}`"
                    :id="`resource-body-${col}`"
                    class="p-2"
                >
                    <resource-tablelist-allinone-item
                        :virtual_indexes="virtual_indexes"
                        :row="row"
                        :col="col"
                        :first_key="first_key == col"
                        :resource_id="resource_id"
                        :resource_route="resource_route"
                    >
                        <template
                            v-slot:after_row="{ after_row, after_row_content }"
                        >
                            <template v-if="row.content.after_row">
                                <el-tooltip
                                    class="item"
                                    effect="dark"
                                    :content="`${
                                        after_row.visible
                                            ? 'Ver Menos'
                                            : 'Ver Mais'
                                    }`"
                                    placement="top"
                                >
                                    <span
                                        class="el-icon-arrow-down arrow-after-icon"
                                        v-if="!after_row.visible"
                                        @click="after_row.visible = true"
                                    />
                                    <span
                                        class="el-icon-arrow-up arrow-after-icon"
                                        v-else
                                        @click="after_row.visible = false"
                                    />
                                </el-tooltip>
                                <portal
                                    :to="`resource_after_row_arrow_${row.content.id}`"
                                >
                                    <transition name="fade">
                                        <div
                                            class="container-fluid content py-3"
                                            v-if="after_row.visible"
                                        >
                                            <v-runtime-template
                                                :template="after_row_content"
                                            />
                                        </div>
                                    </transition>
                                </portal>
                            </template>
                        </template>
                    </resource-tablelist-allinone-item>
                </td>
            </tr>
            <tr
                class="table-row after bg-white"
                :key="`table_after_row_${i}`"
                :id="`resource-body-${row.content.id}-after-row`"
            >
                <td :colspan="columns.length">
                    <portal-target
                        :key="`portal_${i}`"
                        :name="`resource_after_row_arrow_${row.content.id}`"
                    />
                    <div class="spacer" />
                </td>
            </tr>
        </template>
    </tbody>
</template>
<script>
import VRuntimeTemplate from 'v-runtime-template';
export default {
    props: [
        'rows',
        'table_keys',
        'has_actions',
        'show_right_actions_column',
        'resource_id',
        'resource_route',
    ],
    data() {
        return {
            first_key: this.table_keys[0],
            virtual_indexes: {
                action_col: 'action_checkbox_index',
                after_row: 'after_row_index',
                right_actions_column: 'right_actions_index',
            },
        };
    },
    components: {
        'resource-tablelist-allinone-item':
            require('./partials/-resource-list-allinone-row.vue').default,
        'v-runtime-template': VRuntimeTemplate,
    },
    computed: {
        ids() {
            return this.rows.map((x) => x.id);
        },
        columns() {
            let columns = [];
            columns.push(this.virtual_indexes.after_row);
            if (this.has_actions) {
                columns.push(this.virtual_indexes.action_col);
            }
            columns = columns.concat(this.table_keys);
            if (this.show_right_actions_column) {
                columns.push(this.virtual_indexes.right_actions_column);
            }
            return columns;
        },
    },
};
</script>
