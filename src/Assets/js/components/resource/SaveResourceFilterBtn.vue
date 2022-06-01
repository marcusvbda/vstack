<template>
    <div id="save-resource-filter-btn">
        <div v-for="(r, i) in reports" :key="i">
            <div class="report-card-option">
                <div class="delete-btn" @click="deleteReport(i)">
                    <span class="el-icon-error" />
                </div>
                <el-button @click="openReport(r)" plain :type="isCurrentReport(r) ? 'primary' : ''">{{ r.title }}</el-button>
            </div>
        </div>
        <el-button
            class="report-card-option new"
            plain
            type="success"
            small
            v-if="can_create"
            @click="create"
            v-loading="loading"
        >
            Salvar Modelo de Relatório
        </el-button>
    </div>
</template>
<script>
export default {
    props: ["config_data", "filter_report_limit"],
    data() {
        return {
            loading: false,
            reports: this.config_data?.data?.templates ?? [],
            limit: this.filter_report_limit,
        };
    },
    computed: {
        can_create() {
            return this.reports.length < this.limit && !this.filter_exists && this.has_filter;
        },
        filter_exists() {
            return this.reports.filter((x) => this.isCurrentReport(x)).length > 0;
        },
        has_filter() {
            let filters = this.$getUrlParams();
            if (filters?.per_page) {
                delete filters.per_page;
            }
            if (filters?.page_type) {
                delete filters.page_type;
            }
            console.log(filters);
            return Object.keys(filters).length > 0 ?? false;
        },
    },
    methods: {
        isCurrentReport(x) {
            let queryObject = Object.fromEntries(new URLSearchParams(x.query));
            let currentObject = this.getQueryObject();
            Object.keys(currentObject).forEach((key) => {
                if (!currentObject[key]) delete currentObject[key];
            });
            return _.isEqual(queryObject, currentObject);
        },
        openReport(report) {
            if (this.isCurrentReport(report)) return this.$message("Este é o relatório atual");
            this.$loading({ text: "Carregando modelo de relatório ..." });
            window.location.href = window.location.pathname + report.query;
        },
        deleteReport(index) {
            this.$confirm(`Deseja excluir este modelo de filtro ?`, "Confirmação", { closeOnClickModal: false }).then(() => {
                let _reports = this.reports.filter((x, y) => y != index);
                this.saveReports(_reports);
            });
        },
        create() {
            this.$prompt("Digite o nome do modelo de relatório", "Salvar Modelo de Relatório", {
                confirmButtonText: "Salvar",
                cancelButtonText: "Cancelar",
                closeOnClickModal: false,
                inputValidator: (input) => {
                    if (!(input ?? "").trim()) return "Nome do modelo é obrigatório";
                    if (this.reports.filter((x) => x.title == input).length > 0) return "Já existe um modelo com o mesmo nome";
                    return true;
                },
            }).then(({ value }) => {
                this.createReportTemplate({
                    title: value,
                    query: this.getCleanQueryString(),
                });
            });
        },
        getQueryObject() {
            return Object.fromEntries(new URLSearchParams(window.location.search));
        },
        getCleanQueryString() {
            let params = this.getQueryObject();
            Object.keys(params).forEach((key) => {
                if (!params[key]) delete params[key];
            });
            let str =
                "?" +
                Object.keys(params)
                    .map((key) => encodeURIComponent(key) + "=" + encodeURIComponent(params[key]))
                    .join("&");
            return str;
        },
        saveReports(_reports) {
            this.$http
                .post(`${window.location.pathname}/create-report-template`, _reports)
                .then(({ data }) => {
                    this.loading = false;
                    if (data.success) this.reports = data.reports;
                })
                .catch((er) => {
                    this.loading = false;
                });
        },
        createReportTemplate(template) {
            this.loading = true;
            let _reports = this.reports.map((x) => x);
            _reports.push(template);
            this.saveReports(_reports);
        },
    },
};
</script>
<style lang="scss" scoped>
#save-resource-filter-btn {
    display: flex;
    flex-direction: row;
    .report-card-option {
        left: -10px;
        position: relative;
        margin-top: 10px;
        margin-left: 10px;
        &.new {
            border-style: dashed;
            border-color: #909399;
        }
        .delete-btn {
            position: absolute;
            right: -6px;
            top: -8px;
            background-color: #edeff2;
            padding: 2px;
            border-radius: 100%;
            cursor: pointer;
            .el-icon-error {
                color: #e10e0e;
                font-size: 20px;
                opacity: 0.7;
                transition: 0.3s;
                &:hover {
                    opacity: 1;
                }
            }
        }
    }
}
</style>
