<template>
    <tr>
        <td class="width: 1%;">{{ cloned_row.data.file_id }}</td>
        <td>
            <span v-html="getUrlContent(cloned_row)" />
        </td>
        <td>{{ cloned_row.data.file_name }}.{{ cloned_row.data.file_extension }}</td>
        <td>{{ getDueDate(cloned_row) }}</td>
    </tr>
</template>
<script>
export default {
    props: ['row'],
    data() {
        return {
            cloned_row: this.row,
        }
    },
    created() {
        this.createListeningStatusChange()
    },
    methods: {
        createListeningStatusChange() {
            if (this.cloned_row.data.status != 'exporting') return
            if (laravel.user.id && laravel.chat.pusher_key) {
                this.$echo.private(`App.User.${laravel.user.id}`).listen(`.notifications.exporting_status.${this.row.id}`, (n) => {
                    this.cloned_row.data.status = n.config.data.status
                })
            }
        },
        getDueDate(report) {
            if (report.data.status != 'ready') return ' - '
            let date = new Date(report.data.due_date)
            return date.toLocaleString('pt-BR')
        },
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
    },
}
</script>