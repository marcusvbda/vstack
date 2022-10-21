<template>
    <CustomResourceComponent :label="label" :description="description" custom_class="has-on-or-many">
        <RenderHasManyInfo :info="initializedInfo" :disabled="disabled" />
    </CustomResourceComponent>
</template>
<script>

export default {
    props: ["label", "disabled", "description", "field", "form", "info", "limit"],
    data() {
        return {
            loading: true,
            active_name: null,
            initializedInfo: this.info
        };
    },
    created() {
        this.initInfo()
        this.initField()
    },
    computed: {
        editMode() {
            return !this.content?.id ? true : false
        }
    },
    methods: {
        initInfo() {
            console.log(this.populateChildremValue(this.initializedInfo))
        },
        populateChildremValue(child) {
            child.values = [{
                id: 1,
                name: "lorem ipsum"
            }]
            for (let i in child.children) {
                child.children[i] = this.populateChildremValue(child.children[i])
            }
            return child
        },
        initField() {
            if (this.editMode) {
                this.$set(this.form, this.field, [])
                this.loading = false
            }
        }
    }
}
</script>

<style lang="scss">
.has-on-or-many {
    .create-section {
        margin-bottom: 10px;
        height: 72px;
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

    .card-collapse {

        .el-collapse-item__header {
            background-color: #f0f0f0;
            padding: 0 10px;
        }

        .el-collapse-item__content {
            padding: 10px;
            border: 1px solid #ececec;
        }

        .el-collapse-item.is-disabled {
            .el-collapse-item__header {
                color: #303133 !important;
                cursor: pointer !important;

                .el-collapse-item__arrow {
                    display: none !important;
                }
            }
        }
    }
}
</style>
