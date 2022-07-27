<template>
    <el-dialog
        :visible.sync="dialog"
        width="80%"
        :before-close="handleClose"
        :loading="loading"
        :modal-append-to-body="true"
        :lock-scroll="true"
        :append-to-body="true"
        :destroy-on-close="true"
        :close-on-click-modal="false"
        custom-class="resource-crud-dialog"
    >
        <template v-if="crud_content.resource">
            <span slot="title">
                <div class="d-flex flex-column">
                    <b class="el-dialog__title">{{ title }}</b>
                    <small v-if="sub_title" class="text-muted">{{ sub_title }}</small>
                </div>
            </span>
            <v-runtime-template
                v-if="crud_content.resource.page_type == 'create' && crud_content.resource.before_create_slot"
                :template="`<span>${crud_content.resource.before_create_slot}</span>`"
            />
            <v-runtime-template
                v-if="crud_content.resource.page_type == 'edit' && crud_content.resource.before_edit_slot"
                :template="`<span>${crud_content.resource.before_edit_slot}</span>`"
            />
            <resource-crud
                :data="crud_content.data"
                :params="$getUrlParams()"
                :crud_type="crud_type"
                :raw_type="crud_content.resource.page_type"
                :first_btn="crud_content.resource.first_btn"
                :second_btn="crud_content.resource.second_btn"
                :acl="crud_content.resource.acl"
                :dialog="true"
                ref="crud"
            />
            <v-runtime-template
                v-if="crud_content.resource.page_type == 'edit' && crud_content.resource.after_edit_slot"
                :template="`<span>${crud_content.resource.after_edit_slot}</span>`"
            />
            <v-runtime-template
                v-if="crud_content.resource.page_type == 'create' && crud_content.resource.after_create_slot"
                :template="`<span>${crud_content.resource.after_create_slot}</span>`"
            />
            <span slot="footer" class="dialog-footer">
                <el-button-group>
                    <el-button
                        v-if="crud_content.resource.first_btn"
                        :size="crud_content.resource.first_btn.size"
                        :type="crud_content.resource.first_btn.type"
                        @click="$refs.crud.submit(crud_content.resource.first_btn.field)"
                        :loading="action_btn_loading"
                        class="d-flex"
                        :disabled="action_btn_loading"
                    >
                        <div class="d-flex flex-row">
                            <span v-html="crud_content.resource.first_btn.content" />
                        </div>
                    </el-button>
                    <el-button
                        v-if="crud_content.resource.second_btn"
                        :size="crud_content.resource.second_btn.size"
                        :type="crud_content.resource.second_btn.type"
                        @click="$refs.crud.submit(crud_content.resource.second_btn.field)"
                        :loading="action_btn_loading"
                        :disabled="action_btn_loading"
                        class="d-flex"
                    >
                        <div class="d-flex flex-row">
                            <span v-html="crud_content.resource.second_btn.content" />
                        </div>
                    </el-button>
                </el-button-group>
            </span>
        </template>
    </el-dialog>
</template>
<script>
import VRuntimeTemplate from "v-runtime-template";
import { mapGetters } from "vuex";
export default {
    props: ["resource_id", "row_id", "crud_type"],
    data() {
        return {
            dialog: false,
            loading: true,
            crud_content: {},
        };
    },
    components: {
        "v-runtime-template": VRuntimeTemplate,
    },
    computed: {
        ...mapGetters("resource", ["action_btn_loading"]),
        title() {
            return this.crud_content?.resource?.breadcrumb_labels[this.crud_content?.resource?.page_type] ?? "";
        },
        sub_title() {
            return this.crud_content?.resource?.dialog_sub_titles[this.crud_content?.resource?.page_type] ?? "";
        },
    },
    methods: {
        handleClose() {
            this.dialog = false;
        },
        open() {
            let loading = this.$loading({ text: "Carregando ..." });
            this.$http
                .post(`/admin/${this.resource_id}/get-resource-crud-content`, {
                    type: this.row_id ? "edit" : "create",
                    id: this.row_id,
                })
                .then(({ data }) => {
                    this.crud_content = data;
                    this.dialog = true;
                    loading.close();
                });
        },
    },
};
</script>

<style lang="scss">
.resource-crud-dialog {
    .el-dialog__header {
        background-color: rgba(0, 0, 0, 0.03);
        border-bottom: 1px solid rgba(0, 0, 0, 0.125);
    }
    .el-dialog__body {
        padding: 0;
    }
    .card {
        box-shadow: unset !important;
        &.mb-4 {
            margin-bottom: 0 !important;
        }
        .card-header {
            &.crud-card-header {
                display: none;
            }
        }
    }
    .el-dialog__title {
        font-weight: bold;
        color: #4f4f4f;
    }
    .el-dialog__footer {
        padding: 12px;
    }
}
</style>
