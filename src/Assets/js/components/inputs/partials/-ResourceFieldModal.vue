<template>
    <div
        ref="resourcemodal"
        class="modal fade"
        tabindex="-1"
        role="dialog"
        aria-labelledby="myLargeModalLabel"
        aria-hidden="true"
        data-keyboard="false"
        data-backdrop="static"
    >
        <div class="modal-dialog modal-xl" style="max-height: 90%;overflow-y: auto;">
            <div class="modal-content">
                <div class="card h-100 w-100">
                    <div class="card-header modal-header">
                        <div
                            class="d-flex flex-row justify-content-between align-items-center w-100"
                        >
                            <template
                                v-if="rendered_data.can_update"
                            >{{ form.id >0 ? `Edição de ${rendered_data.singular_label}` : `Cadastro de ${rendered_data.label}`}}</template>
                            <template v-else>Visualização</template>
                            <a href="#" @click.prevent="close">
                                <span class="el-icon-close"></span>
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <form
                                    v-if="showing"
                                    class="needs-validation m-0"
                                    novalidate
                                    v-on:submit.prevent="submit"
                                    @keypress.13.prevent
                                >
                                    <div class="row">
                                        <div class="col-12">
                                            <template
                                                v-for="(field,i) in rendered_data.crud_fields"
                                            >
                                                <v-runtime-template
                                                    :key="i"
                                                    :template="field.view"
                                                />
                                            </template>
                                        </div>
                                    </div>
                                    <hr />
                                    <div
                                        class="row"
                                        v-if="(rendered_data.can_update && form.id) || (rendered_data.can_create && !form.id)"
                                    >
                                        <div
                                            class="col-12 d-flex justify-content-between d-flex align-items-center flex-wrap"
                                        >
                                            <div>
                                                <button
                                                    v-if="rendered_data.can_delete"
                                                    @click="destroy"
                                                    class="btn btn-outline-danger btn-sm-block"
                                                    type="button"
                                                >Excluir</button>
                                            </div>
                                            <div
                                                class="d-flex flex-row align-items-center flex-wrap"
                                            >
                                                <a
                                                    @click.prevent="close"
                                                    href="#"
                                                    class="mr-5 text-danger link d-none d-lg-block"
                                                >
                                                    <b>Cancelar</b>
                                                </a>
                                                <button
                                                    class="btn btn-primary btn-sm-block"
                                                    type="sumit"
                                                >Salvar</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import VRuntimeTemplate from "v-runtime-template"

export default {
    props: ["rendered_data", "form", "errors", "resourceData"],
    data() {
        return {
            showing: false,
        }
    },
    components: {
        "v-runtime-template": VRuntimeTemplate,
    },
    methods: {
        close() {
            $(this.$refs.resourcemodal).modal('hide')
            this.$emit('onClose')
            this.showing = false
        },
        show() {
            $(this.$refs.resourcemodal).modal('show').draggable({ handle: ".modal-header" })
            this.$emit('onShow')
            this.showing = true
        },
        submit() {
            this.$confirm(`Deseja Salvar ?`, "Confirmação", {
                confirmButtonText: "Sim",
                cancelButtonText: "Não",
                type: 'warning'
            }).then(() => {
                let loading = this.$loading()
                this.form.resource_id = this.rendered_data.resource_id
                this.$http.post(this.rendered_data.store_route, this.form).then(res => {
                    let data = res.data
                    if (data.message) this.$message({ showClose: true, message: data.message.text, type: data.message.type })
                    this.$message({ showClose: true, message: `Salvo com sucesso`, type: 'success' })
                    this.close()
                    loading.close()
                    this.$emit("onSubmit")
                }).catch(er => {
                    let errors = er.response.data.errors
                    this.errors = errors
                    loading.close()
                })
            }).catch(() => false)
        },
        destroy() {
            this.$confirm(`Confirma Exclusão ?`, "Confirmação", {
                confirmButtonText: "Sim",
                cancelButtonText: "Não",
                type: 'error'
            }).then(() => {
                this.loading = this.$loading()
                this.$http.delete(this.rendered_data.destroy_route.replace("_replace_area_", this.form.id), {}).then(res => {
                    let data = res.data
                    if (data.message) this.$message({ showClose: true, message: data.message.text, type: data.message.type })
                    this.$emit("onDestroy")
                    this.$message({ showClose: true, message: `Excluido com sucesso`, type: 'success' })
                    this.close()
                    loading.close()
                }).catch(er => {
                    this.loading.close()
                    this.$message({
                        message: er.response.data.message,
                        type: 'error'
                    })
                })
            }).catch(() => false)
        }
    }
}
</script>
<style lang="scss" scoped>
.modal {
    &.ui-draggable {
        overflow-x: unset;
        overflow-y: unset;
    }
}
</style>