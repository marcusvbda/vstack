<template>
    <div class="d-flex flex-row flex-wrap cursor-pointer align-items-center justify-content-center" style="min-height: 25px">
        <el-button-group>
            <el-tooltip class="item" effect="dark" content="Clonar" placement="top" v-if="data.can_clone">
                <el-button size="small" plain type="secondary" icon="el-icon-document-copy" @click="goTo(`${data.route}/clone`)" />
            </el-tooltip>
            <el-tooltip class="item" effect="dark" content="Visualizar" placement="top" v-if="data.can_view">
                <el-button size="small" plain type="info" icon="el-icon-search" @click="goTo(data.route)" />
            </el-tooltip>
            <el-tooltip class="item" effect="dark" content="Editar" placement="top" v-if="data.can_update">
                <el-button size="small" plain type="primary" icon="el-icon-edit" @click="goTo(`${data.route}/edit`)" />
            </el-tooltip>
            <el-tooltip class="item" effect="dark" content="Excluir" placement="top" v-if="data.can_delete">
                <el-button size="small" plain type="danger" icon="el-icon-delete" @click.prevent="destroy" />
            </el-tooltip>
        </el-button-group>
    </div>
</template>
<script>
export default {
    props: ['data', 'id'],
    data() {
        return {
            loading: null,
        }
    },
    methods: {
        goTo(route) {
            window.location.href = route
        },
        submitDelete() {
            this.$confirm(`Confirma Exclusão ?`, 'Confirmação', {
                confirmButtonText: 'Sim',
                cancelButtonText: 'Não',
                type: 'error',
            })
                .then(() => {
                    this.loading = this.$loading()
                    this.$http
                        .delete(this.data.route + '/destroy', {})
                        .then((res) => {
                            res = res.data
                            return (window.location.href = res.route)
                        })
                        .catch((er) => {
                            this.loading.close()
                            this.$message({
                                message: er.response.data.message,
                                type: 'error',
                            })
                        })
                })
                .catch(() => false)
        },
        async destroy() {
            for (let i in this.data.before_delete) {
                let action = this.data.before_delete[i]
                let confirmed = !action.confirm ? true : false
                if (!confirmed) {
                    await this.$confirm(action.confirm.message, action.confirm.title, {
                        confirmButtonText: 'Sim',
                        cancelButtonText: 'Não',
                        type: 'error',
                    })
                }
                let response = await this.$http.post(this.data.route + '/before-destroy', { index: i })
                if (response.data?.confirm) {
                    await this.$confirm(response.data.confirm.message, response.data.confirm.title, {
                        confirmButtonText: 'Sim',
                        cancelButtonText: 'Não',
                        type: 'error',
                    })
                } else {
                    if (!response.data.success) {
                        throw this.$message({
                            message: response.data?.message ?? 'Erro',
                            type: 'error',
                        })
                    }
                }
            }
            this.submitDelete()
        },
    },
}
</script>
