<template>
    <div class="d-flex flex-row flex-wrap cursor-pointer align-items-center" style="min-height: 25px">
        <div class="d-flex align-items-center mr-2">
            <span style="font-weight: 600; opacity: 0.4; letter-spacing: -1">ID .: {{ Number(id).pad(8) }}</span>
        </div>
        <el-dropdown trigger="click" :class="`ml-4 ${!isOpened ? 'crud-btns' : ''}`" @visible-change="setVisible">
            <span class="el-dropdown-link" style="color: #5a8dee !important">Ações<i class="el-icon-arrow-down el-icon--right"></i> </span>
            <el-dropdown-menu slot="dropdown">
                <el-dropdown-item icon="el-icon-view" v-if="data.can_view">
                    <a class="link ml-3" :href="data.route">Visualizar </a>
                </el-dropdown-item>
                <el-dropdown-item icon="el-icon-edit" v-if="data.can_update">
                    <a class="link ml-3" v-if="data.can_update" :href="`${data.route}/edit`"> Editar </a>
                </el-dropdown-item>
                <el-dropdown-item icon="el-icon-delete  text-danger" divided v-if="data.can_delete">
                    <a class="link ml-3" v-if="data.can_delete" @click.prevent="destroy" style="color: red !important"> Excluir </a>
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
