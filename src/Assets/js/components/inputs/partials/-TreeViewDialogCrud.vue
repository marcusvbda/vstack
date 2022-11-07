<template>
    <div class="card" v-loading="card_loading" :element-loading-text="loading_text">
        <div class="card-header">
            <div class="d-flex flex-row align-items-center">
                <b>{{ selected.id ? "Edição" : "Cadastro" }} de {{ label }}</b>
                <a href="#" class="close-icon" @click.prevent="close" v-if="!action_btn_loading">
                    <i class="el-icon-close" />
                </a>
            </div>
        </div>
        <template v-if="loading">
            <div class="card-body" style="padding:13px">
                <div class="d-flex flex-column">
                    <div v-for="(i, ix) in qtyfields" :key="ix" class="shimmer resource-tree-item-input w-100" />
                </div>
            </div>
            <div class="card-footer">
                <div class="d-flex flex-row justify-content-between">
                    <div class="shimmer btn-crud-item delete w-25" v-if="selected.id && acl.delete" />
                    <div class="shimmer btn-crud-item w-25 ml-auto"
                        v-if="(selected.id && acl.update) || (!selected.id && acl.create)" />
                </div>
            </div>
        </template>

        <template v-else>
            <div class="card-body" :style="{ padding: `${cards.length > 1 ? '0 13px' : '0'}` }">
                <form class="needs-validation m-0" novalidate v-on:submit.prevent>
                    <table class="table table-crud no-title mb-0">
                        <tbody>
                            <el-tabs v-model="tab" v-if="cards.length > 1">
                                <template v-for="(card, i) in cards">
                                    <el-tab-pane :label="card.label" :key="`tab_${i}`" />
                                </template>
                            </el-tabs>
                            <template v-for="(card, i) in cards">
                                <VRuntimeTemplate v-if="tab == i" :template="card.view"
                                    :key="`tree-view-detail-card-${i}`" />
                            </template>
                        </tbody>
                    </table>
                </form>
            </div>
            <div class="card-footer" v-if="!action_btn_loading">
                <div class="d-flex flex-row justify-content-between">
                    <button class="btn btn-danger btn-crud-item px-5" v-if="selected.id && acl.delete" @click="destroy">
                        <i class="el-icon-delete mr-2" />
                        Excluir
                    </button>
                    <button class="btn btn-secondary btn-crud-item px-5 ml-auto" @click="submit"
                        v-if="(selected.id && acl.update) || (!selected.id && acl.create)">
                        <i class="el-icon-check mr-2" />
                        {{ selected.id ? "Salvar" : "Cadastrar" }}
                    </button>
                </div>
            </div>
        </template>
    </div>
</template>
<script>
import VRuntimeTemplate from "v-runtime-template";
import { mapGetters } from "vuex";

export default {
    props: ["resource", "selected", "label", "qtyfields", "fk_value", "fk_index", "acl"],
    data() {
        return {
            tab: 0,
            loading: true,
            cards: [],
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
        ...mapGetters("resource", ["action_btn_loading"]),
        inputs() {
            let inputs = []
            for (let i in this.cards) {
                let card = this.cards[i]
                for (let y in card.inputs) {
                    let input = card.inputs[y]
                    inputs.push(input)
                }
            }
            return inputs
        },
        inputFieldsIndexes() {
            return this.inputs.map((x) => x.options.field);
        },
    },
    methods: {
        close() {
            this.$emit("close");
        },
        destroy() {
            this.$confirm("Deseja excluir este registro ?", "Confirmação", {
                closeOnClickModal: false,
            }).then(() => {
                this.loading_text = "Excluindo ...";
                this.card_loading = true;
                this.$http
                    .post(`/admin/${this.resource}/${this.selected.code}/destroy`, {
                        input_origin: "resource-tree",
                    })
                    .then(() => {
                        this.$emit("saved", "Registro excluido com sucesso !!");
                    });
            });
        },
        initFields() {
            if (this.selected.id) {
                Object.keys(this.selected).forEach((key) => {
                    if (this.inputFieldsIndexes.includes(key) || key == "id") {
                        this.$set(this.form, key, this.selected[key]);
                    }
                });
            }
            this.$set(this.form, this.fk_index, this.fk_value);
            this.inputs.forEach(input => {
                if (input.options.default !== null) {
                    this.$set(this.form, input.options.field, input.options.default);
                }
            });
        },
        init() {
            const payload = {
                params: { resource: this.resource }
            }
            this.$http.get("/admin/inputs/resource-tree/load-crud", payload).then(({ data }) => {
                this.cards = data;
                this.initFields();
                this.loading = false;
            });
        },
        submit() {
            this.loading_text = "Salvando ...";
            this.card_loading = true;
            this.$http
                .post(`/admin/${this.resource}/store`, {
                    ...this.form,
                    resource_id: this.resource,
                    input_origin: "resource-tree",
                })
                .then(({ data }) => {
                    if (data.success) {
                        return this.$emit("saved", "Registro salvo com sucesso !!");
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


.table-crud {
    &.no-title {
        .el-tabs__header {
            margin-bottom: 0;
            border-bottom: unset;
        }


        .crud-card-header {
            display: none;
        }
    }
}
</style>
