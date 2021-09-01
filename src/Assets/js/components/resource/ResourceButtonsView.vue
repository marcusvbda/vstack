<template>
    <div>
        <button @click.prevent="destroy" v-if="can_delete" class="ml-auto btn btn-danger btn-lg btn-sm-block mb-1">
            <span class="el-icon-delete text-white" />
        </button>
        <a v-if="can_update" :href="update_route" class="btn btn-primary btn-lg btn-sm-block mb-1">
            <span class="el-icon-edit text-white" />
        </a>
    </div>
</template>
<script>
export default {
    props: ["can_delete", "update_route", "can_update", "route_destroy", "breadcrumb"],
    methods: {
        getDestroyRedirect() {
            try {
                let keys = Object.keys(this.breadcrumb);
                let key = keys[keys.length - 2];
                return this.breadcrumb[key];
            } catch {
                let keys = Object.keys(this.breadcrumb);
                let key = keys[0];
                return this.breadcrumb[key];
            }
        },
        destroy() {
            this.$confirm(`Confirma Exclusão ?`, "Confirmação", {
                confirmButtonText: "Sim",
                cancelButtonText: "Não",
                type: "error"
            })
                .then(() => {
                    this.loading = this.$loading();
                    this.$http
                        .delete(this.route_destroy, {})
                        .then(() => {
                            return (window.location.href = this.getDestroyRedirect());
                        })
                        .catch(er => {
                            this.loading.close();
                            this.$message({
                                message: er.response.data.message,
                                type: "error"
                            });
                        });
                })
                .catch(() => false);
        }
    }
};
</script>
