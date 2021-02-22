<template>
    <div>
        <a href="#" @click.prevent="openExportModal">
            <slot />
        </a>
        <el-dialog :title="`Exportação de planilha de ${label}`" :visible.sync="visible" center append-to-body>
            <div class="row d-flex justify-content-center">
                <div class="d-flex flex-row align-items-end">
                    <b>Selecione as colunas que deseja importar em sua planilha</b>
                    <small class="ml-2 text-muted">Será respeitado o filtro da listagem</small>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12">
                    <el-checkbox class="no-check-mgt mx-1" v-for="(f, i) in columns" :key="i" v-model="columns[i].enabled" :label="columns[i].label" border />
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
                <el-button type="primary" @click="confirm">Exportar Planilha</el-button>
            </span>
        </el-dialog>
    </div>
</template>
<script>
export default {
    props: ['id', 'export_columns', 'label', 'qty_results', 'get_params', 'limit', 'config_columns', 'waiting_limit', 'waiting_qty'],
    data() {
        return {
            visible: false,
            columns: {},
            disabled_columns: this.config_columns?.data?.disabled_columns ?? [],
        }
    },
    mounted() {
        this.setColumns()
    },
    methods: {
        setColumns() {
            Object.keys(this.export_columns).map((key) => {
                let _disabled_columns = this.disabled_columns ?? []
                let storage_value = !(_disabled_columns ?? []).includes(key)
                this.$nextTick(() => {
                    this.$set(this.columns, key, {
                        enabled: storage_value,
                        label: this.export_columns[key].label ? this.export_columns[key].label : this.export_columns[key],
                    })
                })
            })
        },
        openExportModal() {
            if (this.waiting_qty >= this.waiting_limit)
                return this.$message(
                    `Você já possui ${this.waiting_qty} relatório${
                        this.waiting_qty > 1 ? 's' : ''
                    } aguardando a exportação, aguarde a finalização para exportar novos.`
                )
            this.visible = true
        },
        confirm() {
            this.$confirm('Exportar relatório para planilha ? ', 'Confirmação').then(() => {
                this.$loading({ text: `Aguarde, Gerando Planilha de ${this.id} ...` })
                this.$http
                    .post(`/admin/${this.id}/export`, {
                        get_params: this.get_params,
                        columns: this.columns,
                    })
                    .then((resp) => {
                        resp = resp.data
                        this.visible = false
                        this.setColumns()
                        if (resp.url) window.open(resp.url, '_BLANK')
                        return window.location.reload()
                    })
            })
        },
    },
}
</script>
<style lang="scss" >
.no-check-mgt {
    .el-checkbox__input {
        margin-top: unset !important;
    }
}
</style>
