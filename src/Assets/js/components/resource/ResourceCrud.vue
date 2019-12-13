<template>
    <div class="row">
        <div class="col-12">
            <form class="needs-validation m-0" novalidate v-on:submit.prevent="submit" >
                <template v-for="(card,i) in data.fields">
                    <v-runtime-template :key="i" :template="card.view" />
                </template>
                    
                <div class="row">
                    <div class="col-12 d-flex justify-content-end d-flex align-items-center flex-wrap">
                        <a :href="data.list_route" class="mr-5 text-danger link d-none d-lg-block"><b>Cancelar</b></a>
                        <button class="btn btn-primary btn-sm-block" type="sumit">{{pageType=='CREATE' ? 'Cadastrar' : 'Alterar'}}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
import VRuntimeTemplate from "v-runtime-template"
export default {
    props:["data"],
    data() {
        return {
            form : {},
            errors : {},
        }
    },
    components : {
        "v-runtime-template" : VRuntimeTemplate
    },
    computed : {
        pageType() {
            return this.data.id ? "EDIT" : "CREATE"
        }
    },
    async created() {
        this.initForm()
    },
    methods : {
        initForm() {
            let fields = []
            for(let i in this.data.fields){
                let card = this.data.fields[i]
                for(let y in card.inputs){
                    fields.push(card.inputs[y])
                }
            }
            for(let i in fields) {
                let field_name = fields[i].options.field
                let field_value = fields[i].options.value ? fields[i].options.value : fields[i].options.default
                console.log(field_name,field_value)
                this.$set(this.form, field_name, field_value)
            }
            this.$set(this.form, "resource_id", this.data.resource_id)
            if(this.data.id) this.$set(this.form, "id", this.data.id)
        },
        submit() {
            this.$confirm(`Confirma ${this.data.page_type} ?`, "Confirmação", {
               confirmButtonText: "Sim",
               cancelButtonText: "Não",
               type: 'warning'
            }).then(() => {
                let loading = this.$loading()
                this.$http.post(this.data.store_route,this.form).then( res => {
                    let data = res.data
                    console.log(this.form.colors)
                    if(data.message) this.$message({showClose: true, message : data.message.text,type: data.message.type})
                    if(data.success) return window.location.href=data.route
                    loading.close()
                }).catch( er => {
                    let errors = er.response.data.errors
                    this.errors = errors
                    loading.close()
                })
            }).catch( () => false)
        }
    }
}
</script>