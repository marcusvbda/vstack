<template>
    <div>
        <div class="row mb-3">
            <div class="col-md-12 d-flex justify-content-between align-items-center flex-wrap">
                <div><slot name="title"></slot></div>
                <button @click.prevent="destroy" v-if="data.can_delete" class="ml-auto btn btn-danger btn-lg btn-sm-block mb-1"><span class="el-icon-delete text-white"></span></button>
                <a v-if="data.can_update" :href="getRoute('update_route')" class="btn btn-primary btn-lg btn-sm-block mb-1"><span class="el-icon-edit text-white"></span></a>
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
                                            <td style="width:25%;" v-if="i.indexOf('IGNORE__')<0"><span v-html="i"></span></td>
                                            <td>
                                                <v-runtime-template :params="params" :key="i" :template="`<span>${field===null ? '' : field}</span>`" />
                                            </td>
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
import VRuntimeTemplate from "v-runtime-template"
export default {
    props:["data","params"],
    data() {
        return {
            loading : false,
            resourceData : {
                CourseModules: {}
            },
        }
    },
    components : {
        "v-runtime-template" : VRuntimeTemplate
    },
    methods : {
        getRoute(route) {
            let query = this.data.params ? `?params%5Bredirect_back%5D=${this.data.params.redirect_back}` : "";
            return this.data[route]+query
        },
        destroy() {
            this.$confirm(`Confirma Exclusão ?`, "Confirmação", {
                confirmButtonText: "Sim",
                cancelButtonText: "Não",
                type: 'error'
            }).then(() => {
                this.loading = this.$loading()
                this.$http.delete(this.data.route_destroy,{}).then( res => {
                    res = res.data
                    return window.location.href=this.data.params ? this.data.params.redirect_back : res.route
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