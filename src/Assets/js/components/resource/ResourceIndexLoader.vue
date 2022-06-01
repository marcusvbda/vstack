<template>
    <div>
        <template v-if="loading">Loading ...</template>
        <template v-else>
            <VRuntimeTemplate :template="template" />
        </template>
    </div>
</template>
<script>
import VRuntimeTemplate from "v-runtime-template";

export default {
    props: ["resource_id"],
    components: {
        VRuntimeTemplate,
    },
    data() {
        return {
            loading: true,
            template: "",
        };
    },
    computed: {
        query_params() {
            return this.$getUrlParams();
        },
    },
    created() {
        setTimeout(() => {
            if (!this._isDestroyed) {
                this.init();
            }
        });
    },
    methods: {
        init() {
            this.loading = true;
            this.$http
                .post(`/admin/${this.resource_id}/list/get-list-data`, this.query_params)
                .then(({ data }) => {
                    this.loading = false;
                    this.template = data.template;
                })
                .catch((error) => {
                    console.log(error);
                    this.loading = false;
                });
        },
    },
};
</script>
