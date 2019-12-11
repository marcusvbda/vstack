<template>
    <div class="card" v-loading="loading">
        <div class="card-header bg-white py-4">
            <div class="row">
                <div class="col-12">
                    <h3 class="font-weight-light">Importar {{data.resource.singular_label.toLowerCase()}} de um arquivo CSV</h3>
                    <div class="mt-3">Esta ferramenta lhe permite importar (ou fundir) {{data.resource.label.toLowerCase()}} para a sua loja a partir de um arquivo CSV.</div>
                    <div>
                        Caso não tenha um deseja um arquivo CSV de modelo para importação
                        <a class="link" href="#" @click.prevent="downloadExample">Clique aqui para efetuar o download</a>
                        </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-5 col-sm-12">Escolha um arquivo CSV do seu computador</div>
                <div class="col-md-6 col-sm-12">
                    <template v-if="!frm.file">
                        <input  accept=".csv" type="file" @change="fileChange">
                        <div><small class="mt-2">Tamanho máximo: 128 MB</small></div>
                    </template>
                    <template v-else>
                        <div class="d-flex flex-row">
                            <div>{{frm.file.name}}</div>
                            <a href="#" class="ml-2 link text-danger" @click.prevent="frm.file=null">Trocar de Arquivo</a>
                        </div>
                    </template>
                </div>
            </div>
            <div class="row mt-4" v-if="config.show_advanced">
                <div class="col-md-5 col-sm-12">Delimitador do CSV</div>
                <div class="col-md-2 col-sm-12">
                    <v-input v-model="config.delimiter"  placeholder="Digite aqui o delimitador do CSV ..." />
                </div>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="row">
                <div class="col-12 d-flex flex-row flex-wrap align-items-center justify-content-between">
                    <a href="#" @click.prevent="config.show_advanced = !config.show_advanced">Exibir opções avançadas</a>
                    <button class="btn btn-primary" @click="next" :disabled="!frm.file" >Continuar</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props:["data","frm","config"],
    data(){
        return {
            loading : false
        }
    },
    methods :{
        fileChange(e) {
            var files = e.target.files || e.dataTransfer.files;
            if (!files.length)
                return;
            this.frm.file = files[0]
        },
        next() {
            this.loading = true
            let data = new FormData()
            data.append("file", this.frm.file)
            data.append("delimiter", this.config.delimiter)
            this.$http.post(this.data.resource.route+"/import/check_file",data).then( res => {
                res = res.data
                this.loading = false
                if(!res.success)  return this.$message({showClose: true, message : res.message,type: "error"})
                this.config.data.csv_header = res.data
                this.config.step++
            }).catch(er =>{
                console.log(er)
                this.loading = false
            })
        },
        downloadExample() {
            let columns = this.config.data.columns
            let text = ""
            for(let i in columns) text+=`${columns[i]},`
            text = text.slice(0,-1)
            let filename = `${this.data.resource.label.toLowerCase()}.csv`
            let element = document.createElement('a')
            element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text))
            element.setAttribute('download', filename)
            element.style.display = 'none'
            document.body.appendChild(element)
            element.click()
            document.body.removeChild(element)
        }
    }
}
</script>