<template>
    <div>
        <a href="#" @click.prevent="openExportModal" id="resource-report-btn">
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
                <div class="flex padding-dialog flex-col gap-3">
                    <b class="dark:text-neutral-200">
                        Selecione as colunas que deseja importar em sua planilha
                    </b>
                    <small class="text-neutral-400">
                        Será respeitado o filtro da listagem
                    </small>
                </div>
                <div
                    class="flex padding-dialog row-dialog-export gap-2"
                    id="row-dialog-export-items"
                >
                    <ElCheckbox
                        v-for="(f, i) in columns"
                        :key="i"
                        v-model="columns[i].enabled"
                        class="mx-0"
                        :label="columns[i].label"
                        border
                        :id="`row-dialog-export-items-${columns[i].label}`"
                    />
                </div>
                <div
                    slot="footer"
                    class="dialog-footer flex flex-col md:flex-row justify-end gap-2"
                >
                    <button
                        @click="visible = false"
                        id="row-dialog-export-cancel"
                        class="vstack-btn secondary"
                    >
                        Cancelar
                    </button>
                    <button
                        type="primary"
                        @click="confirm"
                        id="row-dialog-export-confirm"
                        class="vstack-btn primary"
                    >
                        Exportar Relatório
                    </button>
                </div>
            </template>
            <template v-else>
                <template v-if="exporting.current_action == 'preparing'">
                    <div
                        style="height: 300px"
                        class="flex items-center justify-center"
                    >
                        <span class="text-4xl text-neutral-400">
                            <i class="fas fa-spinner fa-spin" />
                        </span>
                    </div>
                </template>
                <template v-if="exporting.current_action == 'processing'">
                    <div class="py-5">
                        <div class="flex justify-between mb-2 w-full">
                            <div class="text-neutral-400">
                                Exportando {{ exporting.exported }}/{{
                                    exporting.total_results
                                }}
                            </div>
                            <div class="text-neutral-400">
                                Tempo estimado : {{ formated_estimated_time }}
                            </div>
                        </div>
                        <ElProgress
                            :text-inside="true"
                            :stroke-width="24"
                            :percentage="percentage"
                            color="#5b5b5b"
                        />
                        <div class="text-center mt-4">
                            <small
                                class="text-neutral-400 word-break-all"
                                v-html="message"
                            />
                        </div>
                    </div>
                </template>
            </template>
        </ElDialog>
    </div>
</template>
<script>
import Excel from 'exceljs';
import { saveAs } from 'file-saver';

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
            started_time: null,
            estimated_time: 0,
            estimated_timeout: null,
            current_action: 'waiting',
            sheet_created: false,
            new_disabled_columns: [],
            workbook: {},
            worksheet: {},
        },
        humanize_timeout: 1000,
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
    const blob = new Blob([xls64], {
        type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    });
    await saveAs(blob, fileName);
};

export default {
    props: [
        'id',
        'export_columns',
        'label',
        'get_params',
        'config_columns',
        'message',
    ],
    data() {
        return getDefaultData(
            this.config_columns?.data?.disabled_columns ?? []
        );
    },
    computed: {
        formated_estimated_time() {
            if (!this.exporting.estimated_time && this.percentage != 100) {
                return 'Calculando ...';
            }
            if (this.exporting.estimated_time !== null) {
                return this.$moment
                    .utc(this.exporting.estimated_time)
                    .format('HH:mm:ss');
            }
            if (this.exporting.estimated_time < 0 || this.percentage === 100) {
                return this.$moment.utc(0).format('HH:mm:ss');
            }
            return 'Calculando ...';
        },
        percentage() {
            const value = Math.round(
                (this.exporting.exported / this.exporting.total_results) * 100
            );
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
                preparing: `Aguarde, preparando exportação de relatório de ${this.label} ...`,
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
            return Object.keys(this.columns).filter(
                (key) => this.columns[key].enabled
            );
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
                    this.exporting.current_action = 'processing';
                    this.exporting.total_results = data.total;
                    this.exporting.current_page = data.current_page;
                    this.exporting.last_page = data.last_page;
                    this.exporting.per_page = data.per_page;
                    this.exporting.file_name = data.file_name;
                    this.exporting.new_disabled_columns = data.disabled_columns;
                    this.updateEstimatedTime();
                    handleCreateWorkSheet(this.label, this.sheetColumns);
                    this.initPreventClose();
                    this.handleExport();
                },
                next_page: (data) => {
                    this.exporting.current_action = 'processing';
                    this.exporting.current_page++;
                    this.exporting.exported += data.processed_row.length;
                    this.updateEstimatedTime();
                    handleAppendRowToWorkSheet(data.processed_row);
                    this.handleExport(data.next_page ?? null);
                },
                finish: (data) => {
                    // this.exporting.exported = this.exporting.total_results;
                    // handleAppendRowToWorkSheet(data.processed_row);
                    // this.updateEstimatedTime();
                    // clearInterval(this.exporting.estimated_timeout);
                    // window.onbeforeunload = null;
                    // setTimeout(() => {
                    //     handleFinishWorkBook(this.fileName).then(() => {
                    //         this.$message({
                    //             showClose: true,
                    //             message: `Exportação do relatório de ${this.label} concluída !`,
                    //             type: 'success',
                    //         });
                    //         this.resetData();
                    //     });
                    // }, this.humanize_timeout);
                },
            };
            return actions;
        },
    },
    mounted() {
        this.initComponent();
    },
    methods: {
        initPreventClose() {
            window.onbeforeunload = () => {
                return 'Exportação em andamento, se sair agora, a exportação será cancelada e o progresso será perdido.';
            };
        },
        updateEstimatedTime() {
            if (!this.exporting.exported) {
                this.exporting.started_time = new Date().getTime();
                return (this.exporting.estimated_time = 0);
            }
            const now = new Date().getTime();
            const total_time = now - this.exporting.started_time;
            const rest_to_import =
                this.exporting.total_results - this.exporting.exported;
            let estimated_ms = Math.round(
                (rest_to_import * total_time) / this.exporting.exported
            );
            this.exporting.estimated_time = estimated_ms < 0 ? 0 : estimated_ms;

            clearInterval(this.exporting.estimated_timeout);
            this.exporting.estimated_timeout = setInterval(() => {
                if (this.exporting.estimated_time > 1000) {
                    this.exporting.estimated_time -= 1000;
                } else {
                    this.exporting.estimated_time = 0;
                }
            }, 1000);
        },
        async finishExporting() {
            const xls64 = await this.exporting.workbook.xlsx.writeBuffer({
                base64: true,
            });
            const blob = new Blob([xls64], {
                type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            });
            await saveAs(blob, this.fileName);
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
            let disabled_columns = this.disabled_columns ?? [];
            this.export_columns.map((row, key) => {
                let storage_value = !(disabled_columns ?? []).includes(key);
                this.$set(this.columns, key, {
                    enabled: storage_value,
                    label: this.export_columns[key].label
                        ? this.export_columns[key].label
                        : this.export_columns[key],
                });
            });
            workbook = new Excel.Workbook();
            worksheet = null;
        },
        openExportModal() {
            this.visible = true;
        },
        confirm() {
            this.$confirm(
                'Exportar relatório para planilha ? ',
                'Confirmação'
            ).then(() => {
                this.exporting.current_action = 'preparing';
                setTimeout(() => {
                    this.handleExport();
                }, this.humanize_timeout);
            });
        },
        handleExport(pNextPage) {
            this.$http
                .post(
                    pNextPage ?? `/admin/${this.id}/export`,
                    this.exportingParameters
                )
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
