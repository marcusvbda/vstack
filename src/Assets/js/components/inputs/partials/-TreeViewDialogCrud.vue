<template>
    <div class="card" v-loading="card_loading" :element-loading-text="loading_text">
        <div class="card-header">
            <div class="d-flex flex-row align-items-center">
                <b>{{ form.id ? "Edição" : "Cadastro" }} de {{ label }}</b>
                <a href="#" class="close-icon" @click.prevent="close">
                    <i class="el-icon-close" />
                </a>
            </div>
        </div>
        <template v-if="loading">
            <div class="card-body">
                <div class="p-3">
                    <div class="d-flex flex-column">
                        <div v-for="(i, ix) in qtyfields" :key="ix" class="shimmer resource-tree-item-input w-100" />
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex flex-row justify-content-between">
                    <div class="shimmer btn-crud-item delete w-25" v-if="form.id" />
                    <div class="shimmer btn-crud-item w-25 ml-auto" />
                </div>
            </div>
        </template>

        <template v-else>
            <div class="card-body">
                <form class="needs-validation m-0" novalidate v-on:submit.prevent>
                    <table class="table table-crud mb-0">
                        <tbody>
                            <template v-for="(input, i) in inputs">
                                <VRuntimeTemplate :template="input.view" :key="`tree-view-detail-input-${i}`" />
                            </template>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer">
                <div class="d-flex flex-row justify-content-between">
                    <button class="btn btn-danger btn-crud-item px-5" v-if="form.id">
                        <i class="el-icon-delete mr-2" />
                        Excluir
                    </button>
                    <button class="btn btn-secondary btn-crud-item px-5 ml-auto" @click="submit">
                        <i class="el-icon-check mr-2" />
                        {{ form.id ? "Salvar" : "Cadastrar" }}
                    </button>
                </div>
            </div>
        </template>
    </div>
</template>
<script>
import VRuntimeTemplate from "v-runtime-template";

export default {
    props: ["resource", "selected", "label", "qtyfields"],
    data() {
        return {
            loading: true,
            inputs: [],
            errors: [],
            form: {},
            card_loading: false,
            loading_text: "",
        };
    },
    components: {
        VRuntimeTemplate,
    },
    created() {
        this.init();
    },
    computed: {
        inputFieldsIndexes() {
            return this.inputs.map((x) => x.options.field);
        },
    },
    methods: {
        close() {
            this.$emit("close");
        },
        initFields() {
            Object.keys(this.selected).forEach((key) => {
                if (this.selected.id) {
                    if (this.inputFieldsIndexes.includes(key) || key == "id") {
                        this.$set(this.form, key, this.selected[key]);
                    }
                } else {
                    console.log("TIPO CADASTRO");
                }
            });
        },
        init() {
            this.$http.post("/admin/inputs/resource-tree/load-crud", { resource: this.resource }).then(({ data }) => {
                this.inputs = data;
                this.initFields();
                setTimeout(() => {
                    this.loading = false;
                }, 500);
            });
        },
        submit() {
            this.loading_text = "Salvando ...";
            this.card_loading = true;
            this.$http
                .post(`/admin/${this.resource}/store`, {
                    ...this.form,
                    clicked_btn: "resource_tree_save",
                    resource_id: this.resource,
                    input_origin: "resource-tree",
                })
                .then(({ data }) => {
                    if (data.success) {
                        return this.$emit("saved");
                    } else {
                        if (data.message) {
                            this.$message({ showClose: true, message: data.message.text, type: data.message.type });
                        }
                        this.card_loading = false;
                    }
                })
                .catch((er) => {
                    this.makeFormValidationErrors(er);
                    this.card_loading = false;
                });
        },
        makeFormValidationErrors(er) {
            let errors = er.response.data.errors;
            this.errors = errors;
            try {
                let message = Object.keys(errors)
                    .map((key) => `<li>${errors[key][0]}</li>`)
                    .join("");
                this.$message({
                    dangerouslyUseHTMLString: true,
                    showClose: true,
                    message: `<ul>${message}</ul>`,
                    type: "error",
                });
            } catch {
                return;
            }
        },
    },
};
</script>
<style lang="scss">
.tree-view-details {
    .el-dialog__header {
        display: none;
    }

    .el-dialog__body {
        padding: 0;

        .card {
            .card-header {
                padding: 11px;
                .close-icon {
                    margin-left: auto;
                    color: #797979;
                    font-size: 20px;
                    text-decoration: none;
                    font-weight: bold;
                    &:hover {
                        transition: 0.4s;
                        color: black;
                    }
                }
            }
            .card-body {
                padding: 0;

                .resource-tree-item-input {
                    height: 40px;
                    margin-bottom: 10px;
                }
            }

            .card-footer {
                padding: 11px;
                .btn-crud-item {
                    height: 39px;
                }
            }
        }
    }
}
</style>
