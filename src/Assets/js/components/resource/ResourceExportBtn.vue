<template>
    <div>
        <a href="#" @click.prevent="openExportModal">
            <slot />
        </a>
        <el-dialog
            :title="`Exportação de planilha de ${label}`"
            :visible.sync="visible"
            center
            append-to-body
        >
            <div class="row d-flex justify-content-center">
                <div class="d-flex flex-row align-items-end">
                    <b>Selecione as colunas que deseja importar em sua planilha</b>
                    <small class="ml-2 text-muted">Será respeitado o filtro da listagem</small>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12">
                    <el-checkbox
                        class="no-check-mgt mx-1"
                        v-for="(f,i) in columns"
                        :key="i"
                        v-model="columns[i].enabled"
                        :label="columns[i].label"
                        @change="saveColumnLocalStorage(i)"
                        border
                    />
                </div>
            </div>
            <el-alert
                class="mt-4"
                show-icon
                :title="`Esta importação terá mais de ${limit} resultados e por isso o arquivo será enviado ao seu email após a geração !!`"
                type="warning"
                :closable="false"
                v-if="qty_results > limit"
            />
            <span slot="footer" class="dialog-footer d-flex flex-row justify-content-end">
                <el-button @click="visible = false">Cancelar</el-button>
                <el-popconfirm
                    :title="`Deseja gerar esta planilha de ${id} ?`"
                    class="ml-4"
                    confirmButtonText="Sim"
                    cancelButtonText="Não"
                    @onConfirm="confirm"
                >
                    <el-button slot="reference" type="primary">Exportar Planilha</el-button>
                </el-popconfirm>
            </span>
        </el-dialog>
    </div>
</template>
<script>
export default {
    props: ["id", "export_columns", "label", "qty_results", "get_params", "limit"],
    data() {
        return {
            visible: false,
            columns: {}
        }
    },
    created() {
        this.setColumns()
    },
    methods: {
        saveColumnLocalStorage(key) {
            localStorage.setItem(`${this.id}_export_column_option_${key}`, this.columns[key].enabled)
        },
        setColumns() {
            Object.keys(this.export_columns).map(key => {
                let storage_value = localStorage.getItem(`${this.id}_export_column_option_${key}`)
                this.$set(this.columns, key, {
                    enabled: storage_value ? storage_value : true,
                    label: this.export_columns[key].label ? this.export_columns[key].label : this.export_columns[key]
                })
            })
        },
        openExportModal() {
            this.visible = true
        },
        confirm() {
            const loading = this.$loading({ text: `Aguarde, Gerando Planilha de ${this.id} ...` })
            this.$http.post(`/admin/${this.id}/export`, {
                'get_params': this.get_params,
                'columns': this.columns
            }).then(resp => {
                resp = resp.data
                this.$message({ type: resp.message_type, message: resp.message, duration: 9999999999, showClose: true })
                this.visible = false
                this.setColumns()
                loading.close()
                if (resp.url) return window.open(resp.url, "_BLANK")
            })
        }
    }
}
</script>
<style lang="scss" >
.no-check-mgt {
    .el-checkbox__input {
        margin-top: unset !important;
    }
}
</style>
