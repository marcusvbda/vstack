<template>
    <div class="has-on-or-many-card-list">
        <div class="create-section" v-if="canCreate" @click="openCreateModal">
            <span>
                <i class="el-icon-plus mr-2" />
                {{createText}}
            </span>
        </div>

        <div class="limit-row">
            <small>{{values.length}}/{{info.limit}}</small>
        </div>
        <ElDialog :title="createText" :visible.sync="dialogCreate" width="70%" :close-on-press-escape="false"
            class="dialog-has-many" :close-on-click-modal="false" :append-to-body="true">
            <div class="row-responsive-table">
                <ResourceFormRender :fields="info.fields" />
            </div>
            <div class="d-flex flex-row justify-content-between padding-10">
                <button class="btn btn-secondary btn-sm btn-crud-item px-5 ml-auto">
                    Salvar
                </button>
            </div>
        </ElDialog>
    </div>
</template>
<script>

export default {
    props: ["info", "values", "disabled"],
    data() {
        return {
            dialogCreate: false,
        };
    },
    computed: {
        canCreate() {
            return (this.values.length < this.info.limit) && !this.disabled
        },
        createText() {
            return `Adicionar ${this.info.singular_label.toLowerCase()}`
        },
    },
    methods: {
        openCreateModal() {
            this.dialogCreate = true
        }
    }
}
</script>
<style lang="scss" scoped>
.has-on-or-many-card-list {
    .create-section {
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px dashed #cacaca;
        color: #cacaca;
        cursor: pointer;
        transition: .4s;

        &:hover {
            border-color: #a5a5a5;
            color: #a5a5a5;
        }
    }

    .limit-row {
        color: #a5a5a5;
        margin-top: 5px;
        display: flex;
        justify-content: flex-end;
    }
}
</style>