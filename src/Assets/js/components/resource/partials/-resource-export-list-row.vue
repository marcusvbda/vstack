<template>
    <tr>
        <td class="width: 1%;">{{ cloned_row.data.file_id }}</td>
        <td>
            <span v-html="getUrlContent(cloned_row)" />
        </td>
        <td><span v-html="getName(cloned_row)" /></td>
        <td><span v-html="getDueDate(cloned_row)" /></td>
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
        getName(cloned_row) {
            if (cloned_row.data.status == 'exporting') return this.getExportingGif()
            return `${cloned_row.data.file_name}.${cloned_row.data.file_extension}`
        },
        getMicrotime(cloned_row) {
            if (cloned_row.data.status == 'exporting') return this.getExportingGif()
            if (!cloned_row?.data?.microtime?.start?.start && !cloned_row?.data?.microtime?.end) return ` - `
            let time = parseInt(cloned_row.data.microtime.end - cloned_row.data.microtime.start)
            return this.fancyTime(time)
        },
        fancyTime(duration) {
            let hrs = ~~(duration / 3600)
            let mins = ~~((duration % 3600) / 60)
            let secs = ~~duration % 60
            let ret = ''
            if (hrs > 0) {
                ret += '' + hrs + ':' + (mins < 10 ? '0' : '')
            }
            ret += '' + mins + ':' + (secs < 10 ? '0' : '')
            ret += '' + secs
            return ret
        },
        createListeningStatusChange() {
            if (this.cloned_row.data.status != 'exporting') return
            if (laravel.user.id && laravel.chat.pusher_key) {
                this.$echo.private(`App.User.${laravel.user.id}`).listen(`.notifications.exporting_status.${this.row.id}`, (n) => {
                    this.cloned_row.data = n.config.data
                })
            }
        },
        getDueDate(report) {
            if (!['ready', 'error'].includes(report.data.status)) return this.getExportingGif()
            let date = new Date(report.data.due_date)
            return date.toLocaleString('pt-BR')
        },
        getExportingGif() {
            return `
				<div class="d-flex flex-row align-items-center py-2">
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
			`
        },
        getUrlContent(r) {
            if (r.data.status == 'ready') return `<a target='_BLANK' href='${r.data.route}'>Clique aqui efetuar o download</a>`
            if (r.data.status == 'exporting') return this.getExportingGif()
            if (r.data.status == 'error') return `<span class='text-danger'><b class="mr-2">Erro :</b>${r.data.error_message}</span>`

            return r.data.status
        },
    },
}
</script>
