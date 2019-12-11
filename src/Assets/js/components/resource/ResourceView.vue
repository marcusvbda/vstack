<template>
    <div>
        <div class="row mb-3">
            <div class="col-md-12 d-flex justify-content-between align-items-center">
                <div><slot name="title"></slot></div>
                <div>
                    <button @click.prevent="destroy" v-if="data.can_delete" class="btn btn-danger btn-lg btn-sm-block mr-3"><span class="el-icon-delete text-white"></span></button>
                    <a v-if="data.can_update" :href="data.update_route" class="btn btn-primary btn-lg btn-sm-block"><span class="el-icon-edit text-white"></span></a>
                </div>
            </div>
        </div>
        <div class="row mb-4" v-for="(card, i) in data.fields">
            <div class="col-12">
                <h4 v-if="card.label" v-html="card.label"></h4>
                <div class="card" >
                    <div class="card-body p-0">
                        <div class="row" >
                            <div class="col-md-12">
                                <table class="table table-striped mb-0">
                                    <tbody>
                                        <tr v-for="(field, i) in card.inputs">
                                            <td style="width:25%;"><span v-html="i"></span></td>
                                            <td><span v-html="field"></span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props:["data"],
    data() {
        return {
            loading : false
        }
    },
    methods : {
        destroy() {
            this.$confirm(`Confirma Exclusão ?`, "Confirmação", {
                confirmButtonText: "Sim",
                cancelButtonText: "Não",
                type: 'error'
            }).then(() => {
                this.loading = this.$loading()
                this.$http.delete(this.data.route_destroy,{}).then( res => {
                    res = res.data
                    return window.location.href=res.route
                }).catch( er => {
                    this.loading.close()
                    this.$message({
                        message: er.response.data.message,
                        type: 'error'
                    });
                })
            }).catch( () => false)
        }
    }
}
</script>