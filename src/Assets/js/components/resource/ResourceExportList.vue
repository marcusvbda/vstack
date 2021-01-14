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
                            <tr v-for="(r, i) in report_exports" :key="i">
                                <td class="width: 1%;">{{ r.data.file_id }}</td>
                                <td>
                                    <span v-html="getUrlContent(r)" />
                                </td>
                                <td>{{ r.data.file_name }}.{{ r.data.file_extension }}</td>
                                <td>{{ getDueDate(r) }}</td>
                            </tr>
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
    methods: {
        getUrlContent(r) {
            if (r.data.status == 'ready') return `<a target='_BLANK' href='${r.data.route}'>Clique aqui efetuar o download</a>`
            if (r.data.status == 'exporting')
                return `
				<div class="d-flex flex-row align-items-center">
					<div class="d-flex flex-row align-items-center mr-1">
						<div class="spinner-grow spinner-grow-sm text-muted mr-2" style="zoom:.5;" role="status">
							<span class="sr-only">Loading...</span>
						</div>
						<div class="spinner-grow spinner-grow-sm text-muted mr-2" style="zoom:.5;" role="status">
							<span class="sr-only">Loading...</span>
						</div>
						<div class="spinner-grow spinner-grow-sm text-muted mr-2" style="zoom:.5;" role="status">
							<span class="sr-only">Loading...</span>
						</div>
					</div>
					<span class="text-muted">Processando</span>
				</div>
			`
            if (r.data.status == 'error') return `<span class='text-danger'><b class="mr-2">Erro :</b>${r.data.error_message}</span>`

            return r.data.status
        },
        getDueDate(report) {
            if (report.data.status != 'ready') return ' - '
            let date = new Date(report.data.due_date)
            return date.toLocaleString('pt-BR')
        },
    },
}
</script>