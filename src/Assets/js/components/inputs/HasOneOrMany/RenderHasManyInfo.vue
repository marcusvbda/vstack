<template>
    <CollapseCard>
        <template slot="header">
            <i class="el-icon-s-unfold mr-2" />
            <b>{{info.label}}</b>
        </template>
        <template slot="body">
            <div class="create-section" @click="openSelectDialog" v-if="canSelect">
                <span>
                    <i class="el-icon-plus mr-2" />
                    {{selectText}}
                </span>
            </div>

            <ElDialog :title="createText" :visible.sync="form_visible" width="70%" :close-on-press-escape="false"
                class="dialog-has-many" :close-on-click-modal="false" :append-to-body="true">
                <form class="needs-validation m-0" novalidate v-on:submit.prevent>
                    <div class="row-responsive-table">
                        <ResourceFormRender :fields="info.fields" ref="form_rendered" :errors="validation_errors" />
                    </div>
                    <div class="d-flex flex-row justify-content-between padding-10">
                        <button class="btn btn-secondary btn-sm btn-crud-item px-5 ml-auto" @click="submit">
                            Salvar
                        </button>
                    </div>
                </form>
            </ElDialog>


            <ResourceItemSelect :resource="info.resource" :label="info.label" :singular_label="info.singular_label"
                :resource_id="info.resource_id" :table="info.table" ref="select_resource"
                @on-selected="row => $emit('on-selected', row)" :ignore_ids="selectedIds">
                <template slot="actions">
                    <el-button size="mini" plain class="mr-3" @click="openCreateDialog">
                        <i class="el-icon-plus mr-2" />
                        Adicionar
                    </el-button>
                </template>
            </ResourceItemSelect>

            <CollapseCard v-for="(item,i) in info.values" :key="i" :disabled="info.children.length <= 0"
                custom_class="resource_item">
                <template slot="header">
                    <VRuntimeTemplate :template="info.item_label ? info.item_label : `<span>${item.name}</span>`" />
                </template>
                <template slot="body">
                    <div class="p-3">
                        <RenderHasManyInfo v-for=" (child,i) in info.children" :key="i" :info="child"
                            :disabled="disabled" />
                    </div>
                </template>
            </CollapseCard>
        </template>
    </CollapseCard>
</template>
<script>
import VRuntimeTemplate from "v-runtime-template";

export default {
    props: ["info", "disabled"],
    data() {
        return {
            form_visible: false,
            submiting: false,
            validation_errors: {}
        }
    },
    components: {
        VRuntimeTemplate
    },
    computed: {
        selectText() {
            return `Selecionar ${this.info.singular_label.toLowerCase()}  -  ${this.info.values.length}/${this.info.limit}`
        },
        selectedIds() {
            return this.info.values.map(x => x.id)
        },
        createText() {
            return `Cadastro de ${this.info.label}`
        },
        canSelect() {
            return (this.info.values.length < this.info.limit) && !this.disabled
        },
    },
    methods: {
        openSelectDialog() {
            this.$refs.select_resource.setVisible(true)
        },
        openCreateDialog() {
            this.form_visible = true;
        },
        submit() {
            const payload = this.$refs.form_rendered.form
            this.$http.post(`/admin/${this.info.resource_id}/pre-validate`, payload).then(() => {
                this.info.values.push(payload)
                this.form_visible = false
                this.$refs.form_rendered.form = {}
                this.$refs.select_resource.setVisible(false)
                this.$message({ showClose: true, message: "Registro adicionado !", type: "success" });
            }).catch(err => {
                this.validation_errors = err.response.data.errors;
            })
        }
    }
}
</script>
<style lang="scss">
.el-collapse-item {
    .resource_item {
        border: 1px solid #a2a2a2;
    }
}
</style>