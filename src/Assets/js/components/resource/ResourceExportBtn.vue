<template>
    <div>
        <a href="#" @click.prevent="openExportModal">
            <slot />
        </a>
        <ElDialog
            :close-on-press-escape="false"
            :close-on-click-modal="false"
            :title="titleDialog"
            :visible.sync="visible"
            center
            append-to-body
            :show-close="exporting.current_action == 'waiting'"
        >
            <template v-if="exporting.current_action == 'waiting'">
                <div class="row d-flex justify-content-center">
                    <div class="col-12 padding-dialog d-flex flex-row justify-content-between">
                        <b>Selecione as colunas que deseja importar em sua planilha</b>
                        <small class="ml-4 text-muted">Será respeitado o filtro da listagem</small>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12 padding-dialog row-dialog-export">
                        <ElCheckbox
                            class="no-check-mgt mx-1"
                            v-for="(f, i) in columns"
                            :key="i"
                            v-model="columns[i].enabled"
                            :label="columns[i].label"
                            border
                        />
                    </div>
                </div>
                <span slot="footer" class="dialog-footer d-flex flex-row justify-content-end">
                    <ElButton @click="visible = false">Cancelar</ElButton>
                    <ElButton type="primary" @click="confirm">Exportar Relatório</ElButton>
                </span>
            </template>
            <template v-else>
                <template v-if="exporting.current_action == 'preparing'">
                    <div class="loading-ballls d-flex flex-row align-items-center justify-content-center py-5">
                        <div class="spinner-grow spinner-grow-xs text-muted mr-2" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="spinner-grow spinner-grow-xs text-muted mr-2" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="spinner-grow spinner-grow-xs text-muted mr-2" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                    </div>
                </template>
                <template v-if="exporting.current_action == 'processing'">
                    <div class="py-5">
                        <div class="text-muted mb-2">Exportando {{ exporting.exported }}/{{ exporting.total_results }}</div>
                        <ElProgress :text-inside="true" :stroke-width="24" :percentage="percentage" :color="customColorMethod" />
                        <div class="text-center mt-4">
                            <small class="text-muted" v-html="message" />
                        </div>
                    </div>
                </template>
            </template>
        </ElDialog>
    </div>
</template>
<script>
import Excel from "exceljs";
import { saveAs } from "file-saver";

function getDefaultData(disabled_columns) {
    return {
        visible: false,
        columns: {},
        disabled_columns: disabled_columns,
        exporting: {
            current_page: 0,
            last_page: 0,
            per_page: 0,
            total_results: 0,
            exported: 0,
            current_action: "waiting",
            sheet_created: false,
            new_disabled_columns: [],
            workbook: {},
            worksheet: {},
        },
        timeout: 500,
        humanize_timeout: 2000,
    };
}

let workbook = null;
let worksheet = null;

const handleCreateWorkSheet = (label, columns) => {
    worksheet = workbook.addWorksheet(label);
    worksheet.columns = columns;
};

const handleAppendRowToWorkSheet = (rows) => {
    worksheet.addRows(rows);
};

const handleFinishWorkBook = async (fileName) => {
    const xls64 = await workbook.xlsx.writeBuffer({ base64: true });
    const blob = new Blob([xls64], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
    await saveAs(blob, fileName);
};

export default {
    props: ["id", "export_columns", "label", "get_params", "config_columns", "message"],
    data() {
        return getDefaultData(this.config_columns?.data?.disabled_columns ?? []);
    },
    computed: {
        percentage() {
            const value = Math.round((this.exporting.exported / this.exporting.total_results) * 100);
            if (value < 0) {
                return 0;
            }
            if (value > 100) {
                return 100;
            }
            return value;
        },
        titleDialog() {
            const actions = {
                waiting: `Exportação de relatório de ${this.label}`,
                preparing: `Agaurde, preparando exportação de relatório de ${this.label} ...`,
                processing: `Aguarde, exportando relatório de ${this.label} ...`,
            };
            return actions[this.exporting.current_action] ?? ``;
        },
        exportingParameters() {
            return {
                exporting: this.exporting,
                get_params: this.get_params,
                columns: this.columns,
            };
        },
        enabledColumns() {
            return Object.keys(this.columns).filter((key) => this.columns[key].enabled);
        },
        sheetColumns() {
            const columns = this.enabledColumns.map((col) => ({
                header: this.columns[col].label ?? col,
                width: 30,
            }));
            return columns;
        },
        fileName() {
            return `${this.label}_${new Date().toLocaleDateString()}`;
        },
        actions() {
            const actions = {
                set_totals: (data) => {
                    this.exporting.current_action = "processing";
                    this.exporting.total_results = data.total;
                    this.exporting.current_page = data.current_page;
                    this.exporting.last_page = data.last_page;
                    this.exporting.per_page = data.per_page;
                    this.exporting.file_name = data.file_name;
                    this.exporting.new_disabled_columns = data.disabled_columns;
                    handleCreateWorkSheet(this.label, this.sheetColumns);
                    setTimeout(() => {
                        this.handleExport();
                    }, this.timeout);
                },
                next_page: (data) => {
                    this.exporting.current_action = "processing";
                    this.exporting.current_page++;
                    this.exporting.exported += data.processed_row.length;
                    handleAppendRowToWorkSheet(data.processed_row);
                    setTimeout(() => {
                        this.handleExport();
                    }, this.timeout);
                },
                finish: () => {
                    this.exporting.exported = this.exporting.total_results;
                    setTimeout(() => {
                        handleFinishWorkBook(this.fileName).then(() => {
                            this.$message({
                                showClose: true,
                                message: `Exportação do relatório de ${this.label} concluída !`,
                                type: "success",
                            });
                            this.resetData();
                        });
                    }, this.humanize_timeout);
                },
            };
            return actions;
        },
    },
    mounted() {
        this.initComponent();
    },
    methods: {
        async finishExporting() {
            const xls64 = await this.exporting.workbook.xlsx.writeBuffer({ base64: true });
            const blob = new Blob([xls64], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });
            await saveAs(blob, this.fileName);
        },
        customColorMethod(percentage) {
            if (percentage < 60) {
                return "#909399";
            } else if (percentage < 80) {
                return "#e6a23c";
            } else {
                return "#67c23a";
            }
        },
        resetData() {
            const disbled_columns = this.exporting.new_disabled_columns;
            const default_data = getDefaultData(disbled_columns);
            const keys = Object.keys(default_data);
            for (let i = 0; i < keys.length; i += 1) {
                this.$set(this, keys[i], default_data[keys[i]]);
            }
            this.initComponent();
        },
        initComponent() {
            Object.keys(this.export_columns).map((key) => {
                let _disabled_columns = this.disabled_columns ?? [];
                let storage_value = !(_disabled_columns ?? []).includes(key);
                this.$nextTick(() => {
                    this.$set(this.columns, key, {
                        enabled: storage_value,
                        label: this.export_columns[key].label ? this.export_columns[key].label : this.export_columns[key],
                    });
                });
            });
            workbook = new Excel.Workbook();
            worksheet = null;
        },
        openExportModal() {
            this.visible = true;
        },
        confirm() {
            this.$confirm("Exportar relatório para planilha ? ", "Confirmação").then(() => {
                this.exporting.current_action = "preparing";
                setTimeout(() => {
                    this.handleExport();
                }, this.humanize_timeout);
            });
        },
        handleExport() {
            this.$http
                .post(`/admin/${this.id}/export`, this.exportingParameters)
                .then(({ data }) => {
                    const { action } = data;
                    return this.actions[action] && this.actions[action](data);
                })
                .catch((err) => {
                    console.log(err);
                    return this.$message.error(err.message);
                });
        },
    },
};
</script>
<style lang="scss">
.no-check-mgt {
    .el-checkbox__input {
        margin-top: unset !important;
    }
}

.padding-dialog {
    padding: 20px 12px 20px 12px !important;
}

.row-dialog-export {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: wrap !important;
}
</style>
