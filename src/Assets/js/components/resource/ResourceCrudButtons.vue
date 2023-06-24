<template>
    <div
        class="flex flex-wrap cursor-pointer align-center justify-center"
        style="min-height: 25px"
        :id="`resource-crud-btns-${data.id}`"
    >
        <el-button-group>
            <template v-for="(extra, i) in data.additional_extra_buttons">
                <el-tooltip
                    class="item"
                    effect="dark"
                    :content="extra.title ? extra.title : 'Extra Action'"
                    placement="top"
                    :key="i"
                >
                    <el-button
                        size="small"
                        class="extra-action-btn"
                        plain
                        :style="{
                            backgroundColor: extra.bg_color
                                ? extra.bg_color
                                : 'white',
                            borderColor: extra.border_color
                                ? extra.border_color
                                : '#dedede',
                            color: extra.color ? extra.color : '#606266',
                        }"
                        :icon="extra.icon ? extra.icon : 'el-icon-more'"
                        id="resource-btn-copy"
                        @click="clickedActionExtra(extra)"
                    />
                </el-tooltip>
            </template>

            <el-tooltip
                class="item"
                effect="dark"
                content="Clonar"
                placement="top"
                v-if="data.can_clone"
            >
                <el-button
                    size="small"
                    plain
                    type="secondary"
                    icon="el-icon-document-copy"
                    @click="clickedClone"
                    id="resource-btn-copy"
                />
            </el-tooltip>
            <el-tooltip
                class="item"
                effect="dark"
                content="Visualizar"
                placement="top"
                v-if="data.can_view"
            >
                <el-button
                    size="small"
                    plain
                    type="info"
                    icon="el-icon-search"
                    @click="goTo(data.route)"
                    id="resource-btn-view"
                />
            </el-tooltip>
            <el-tooltip
                class="item"
                effect="dark"
                content="Editar"
                placement="top"
                v-if="data.can_update"
            >
                <el-button
                    size="small"
                    plain
                    type="primary"
                    icon="el-icon-edit"
                    @click="goToEdit()"
                    id="resource-btn-edit"
                />
            </el-tooltip>
            <el-tooltip
                class="item"
                effect="dark"
                content="Excluir"
                placement="top"
                v-if="data.can_delete"
            >
                <el-button
                    size="small"
                    plain
                    type="danger"
                    icon="el-icon-delete"
                    @click.prevent="destroy"
                    id="resource-btn-delete"
                />
            </el-tooltip>
        </el-button-group>
    </div>
</template>
<script>
export default {
    props: ['data', 'resource_id'],
    data() {
        return {
            loading: null,
        };
    },
    methods: {
        clickedActionExtra(extra) {
            if (extra.action_type == 'redirect') {
                window.location.href = extra.url ? extra.url : '#';
                return;
            }
            if (extra.action_type == 'script') {
                return eval(extra.script);
            }
            if (extra.action_type == 'post') {
                return this.$confirm(
                    extra.confirm_message
                        ? extra.confirm_message
                        : 'Confirmar ?',
                    extra.confirm_title ? extra.confirm_title : 'Confirmação',
                    {
                        confirmButtonText: 'Sim',
                        cancelButtonText: 'Não',
                        type: 'warning',
                    }
                ).then(() => {
                    this.$loading({ text: 'Aguarde ...' });
                    this.$http.post(extra.url ? extra.url : '#').then(() => {
                        window.location.reload();
                    });
                });
            }
        },
        goToEdit() {
            return (window.location.href = `${this.data.route}/edit`);
        },
        goTo(route) {
            window.location.href = route;
        },
        submitDelete() {
            this.$confirm(
                `Confirma Exclusão deste registro de ${this.data.resource_singular_label.toLowerCase()} ?`,
                'Confirmação',
                {
                    confirmButtonText: 'Sim',
                    cancelButtonText: 'Não',
                    type: 'error',
                }
            )
                .then(() => {
                    this.loading = this.$loading({ text: 'Aguarde ...' });
                    this.$http
                        .delete(this.data.route + '/destroy', {})
                        .then(({ data }) => {
                            if (data.success) {
                                return (window.location.href = data.route);
                            } else {
                                if (data.message) {
                                    this.$message({
                                        showClose: true,
                                        message: data.message.text,
                                        type: data.message.type,
                                        dangerouslyUseHTMLString: true,
                                    });
                                }
                                this.loading.close();
                            }
                        })
                        .catch((er) => {
                            this.loading.close();
                            this.$message({
                                message: er.response.data.message,
                                type: 'error',
                            });
                        });
                })
                .catch(() => false);
        },
        async destroy() {
            for (let i = 0; i < this.data.before_delete.length; i++) {
                let action = this.data.before_delete[i];
                let confirmed = !action.confirm ? true : false;
                if (!confirmed) {
                    await this.$confirm(
                        action.confirm.message,
                        action.confirm.title,
                        {
                            confirmButtonText: 'Sim',
                            cancelButtonText: 'Não',
                            type: 'error',
                        }
                    );
                }
                let response = await this.$http.post(
                    this.data.route + '/before-destroy',
                    { index: i }
                );
                if (response.data?.confirm) {
                    await this.$confirm(
                        response.data.confirm.message,
                        response.data.confirm.title,
                        {
                            confirmButtonText: 'Sim',
                            cancelButtonText: 'Não',
                            type: 'error',
                        }
                    );
                } else {
                    if (!response.data.success) {
                        throw this.$message({
                            message: response.data?.message ?? 'Erro',
                            type: 'error',
                        });
                    }
                }
            }
            this.submitDelete();
        },
        clickedClone() {
            this.$confirm(
                `Deseja mesmo clonar esse registro de ${this.data.resource_singular_label.toLowerCase()} e suas configurações ?`,
                'Confirmação',
                {
                    confirmButtonText: 'Sim',
                    cancelButtonText: 'Não',
                    type: 'error',
                }
            ).then(() => {
                this.$loading({ text: 'Clonando ...' });
                this.$http.post(this.data.route + '/clone').then(({ data }) => {
                    if (data.success) {
                        window.location.href = data.route;
                    }
                });
            });
        },
    },
};
</script>
<style lang="scss">
.extra-action-btn {
    &:hover {
        transition: 0.4s;
        filter: brightness(90%);
    }
}
</style>
