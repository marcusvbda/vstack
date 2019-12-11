<template>
    <div class="d-flex flex-row flex-wrap cursor-pointer" style="min-height: 25px;">
        <div class="crud-btns d-flex align-items-center mr-2" >
            <span style="font-weight: 600;opacity: .4;letter-spacing: -1;">ID .: {{Number(id).pad(8)}}</span>
        </div>
        <a class="link mr-1 crud-btns d-flex align-items-center" v-if="data.can_view" :href="data.route" >
            <span class="mr-2" style="opacity:.4;color:black;">|</span>
            <span class="el-icon-view pr-1"></span>Visualizar
        </a>
        <a class="link mx-1 crud-btns d-flex align-items-center" v-if="data.can_update" :href="`${data.route}/edit`">
            <span class="mr-2" style="opacity:.4;color:black;">|</span>
            <span class="el-icon-edit pr-1"></span>Editar
        </a>
        <a class="link mx-1 text-danger crud-btns d-flex align-items-center" style="text-decoration:underline;" v-if="data.can_delete" @click.prevent="destroy">
            <span class="mr-2" style="opacity:.4;color:black;">|</span>
            <span class="el-icon-delete pr-1"></span>Excluir
        </a>
    </div>
</template>
<script>
export default {
    props:["data","id"],
    data() {
        return {
            loading : null
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
                this.$http.delete(this.data.route+"/destroy",{}).then( res => {
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