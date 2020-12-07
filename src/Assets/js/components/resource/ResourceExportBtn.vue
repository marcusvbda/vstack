<template>
    <div class="d-flex flex-row flex-wrap cursor-pointer align-items-center" style="min-height: 25px">
        <div class="d-flex align-items-center mr-2">
            <span style="font-weight: 600; opacity: 0.4; letter-spacing: -1">ID .: {{ Number(id).pad(8) }}</span>
        </div>
        <el-dropdown trigger="click" :class="`ml-4 ${!isOpened ? 'crud-btns' : ''}`" @visible-change="setVisible">
            <span class="el-dropdown-link" style="color: #5a8dee !important">Ações<i class="el-icon-arrow-down el-icon--right"></i> </span>
            <el-dropdown-menu slot="dropdown">
                <el-dropdown-item v-if="data.can_view" style="padding: 0">
                    <a class="link" :href="data.route" style="padding: 0 40px"> <span class="el-icon-view mr-3" />Visualizar </a>
                </el-dropdown-item>
                <el-dropdown-item v-if="data.can_update" style="padding: 0">
                    <a class="link" :href="`${data.route}/edit`" style="padding: 0 40px"> <span class="el-icon-edit mr-3" /> Editar </a>
                </el-dropdown-item>
                <el-dropdown-item v-if="data.can_delete" style="padding: 0; border-top: 1px solid #e9e9e9; margin-top: 10px">
                    <a class="link" @click.prevent="destroy" style="color: red !important; padding: 0 40px">
                        <span class="el-icon-delete text-danger mr-3" />
                        Excluir
                    </a>
                </el-dropdown-item>
            </el-dropdown-menu>
        </el-dropdown>
    </div>
</template>
<script>
export default {
    props: ['data', 'id'],
    data() {
        return {
            isOpened: false,
            loading: null,
        }
    },
    methods: {
        setVisible(val) {
            this.isOpened = val
        },
        destroy() {
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
    },
}
</script>
