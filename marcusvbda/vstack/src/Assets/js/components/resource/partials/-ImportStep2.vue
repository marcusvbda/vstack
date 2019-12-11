<template>
    <div class="card" v-loading="loading">
        <div class="card-body p-0">
            <div class="row">
                <div class="col-12 d-flex flex-column justify-content-center align-items-center my-5">
                    <h1 style="font-size: 150px;" class="text-success"><span class="el-icon-success"></span></h1>
                    <h4>Importação de CSV em execução, você será notificado quando concluir.</h4>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="row">
                <div class="col-12 d-flex flex-row flex-wrap align-items-center justify-content-end">
                    <a class="btn btn-primary" :href="data.resource.route" >Ver {{data.resource.label.toLowerCase()}}</a>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props:["data","frm","config"],
    data() {
        return {
            loading : false
        }
    },
    async created() {
        this.loading = true
        this.submit()
    },
    methods :{
        submit() {
            this.loading = true
            let data = new FormData()
            data.append("file", this.frm.file)
            data.append("config", JSON.stringify(this.config))
            this.$http.post(this.data.resource.route+"/import/submit",data).then( res => {
                res = res.data
                this.loading = false
                if(!res.success)  return this.$message({showClose: true, message : res.message,type: "error"})
            }).catch(er =>{
                console.log(er)
                this.loading = false
            })
        },
    }
}
</script>