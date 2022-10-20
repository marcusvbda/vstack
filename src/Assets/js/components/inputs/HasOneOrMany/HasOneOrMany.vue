<template>
    <CustomResourceComponent :label="label" :description="description" custom_class="has-on-or-many">
        <template v-if="loading">
            <div class="shimmer resource-tree" />
        </template>
        <template v-else>
            <HasOneOrManyCardList :info="info" :values="values" :disabled="disabled" />
        </template>
    </CustomResourceComponent>
</template>
<script>
export default {
    props: ["label", "disabled", "description", "field", "form", "info", "limit"],
    data() {
        return {
            loading: true,
            values: []
        };
    },
    created() {
        this.initField()
    },
    computed: {
        editMode() {
            return !this.content?.id ? true : false
        }
    },
    methods: {
        initField() {
            if (this.editMode) {
                this.$set(this.form, this.field, [])
                this.loading = false

                // console.log("initField", this.fields)
            }
        }
    }
}
</script>

<style lang="scss">
.has-on-or-many {
    padding: 0;

    &.resource-tree {
        height: 350px;
        width: 100%;
        border-radius: 5px;
    }

    &.resource-tree-item {
        height: 24px;
    }
}
</style>
