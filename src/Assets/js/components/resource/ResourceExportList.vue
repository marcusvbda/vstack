<template>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header p-0">
                    <div class="col-md-9 col-sm-12">
                        <div class="d-flex flex-row my-1" style="font-size: 14px">
                            <b>Exportações de Planilhas</b>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="thead-dark">
                            <tr>
                                <th>#</th>
                                <th>Url</th>
                                <th>Nome</th>
                                <th>Validade da Url</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr is="resource-export-list-row" v-for="(row, i) in report_exports" :key="i" :row="row" />
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ['report_exports'],
    components: {
        'resource-export-list-row': require('./partials/-resource-export-list-row.vue').default,
    },
}
</script>