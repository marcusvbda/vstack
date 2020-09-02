<template>
    <div class="card" v-loading="loading">
        <div class="card-header bg-white py-4">
            <div class="row">
                <div class="col-12">
                    <h3
                        class="font-weight-light"
                    >Importar planilha de {{data.resource.label.toLowerCase()}}</h3>
                    <div
                        class="mt-3"
                    >Esta ferramenta permite importar {{data.resource.label.toLowerCase()}} a partir de uma planilha.</div>
                    <div>
                        <a
                            class="link"
                            href="#"
                            @click.prevent="downloadExample"
                        >Clique aqui para efetuar o download</a>
                        do modelo de importação
                    </div>
                    <div>Deixe a coluna ID em branco a não ser que seja um caso de alteração de registro, ai esta linha será alterada ao invés cadastrada</div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-5 col-sm-12">Escolha um arquivo XLS do seu computador</div>
                <div class="col-md-6 col-sm-12">
                    <template v-if="!frm.file">
                        <input accept=".xls" type="file" @change="fileChange" />
                        <div>
                            <small class="mt-2">Tamanho máximo: 128 MB</small>
                        </div>
                    </template>
                    <template v-else>
                        <div class="d-flex flex-row">
                            <div>{{frm.file.name}}</div>
                            <a
                                href="#"
                                class="ml-2 link text-danger"
                                @click.prevent="frm.file=null"
                            >Trocar de Arquivo</a>
                        </div>
                    </template>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="row">
                <div
                    class="col-12 d-flex flex-row flex-wrap align-items-center justify-content-end"
                >
                    <button class="btn btn-primary" @click="next" :disabled="!frm.file">Continuar</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ["data", "frm", "config"],
    data() {
        return {
            loading: false
        }
    },
    methods: {
        fileChange(e) {
            var files = e.target.files || e.dataTransfer.files
            if (!files.length)
                return
            this.frm.file = files[0]
        },
        next() {
            this.loading = true
            let data = new FormData()
            data.append("file", this.frm.file)
            data.append("delimiter", this.config.delimiter)
            this.$http.post(this.data.resource.route + "/import/check_file", data).then(res => {
                res = res.data
                this.loading = false
                if (!res.success) return this.$message({ showClose: true, message: res.message, type: "error" })
                this.config.data.csv_header = res.data
                this.config.step++
            }).catch(er => {
                console.log(er)
                this.loading = false
            })
        },
        downloadExample() {
            window.open(`/admin/${this.data.resource.resource_id}/import/sheet_template`, '_blank')
        }
    }
}
</script>