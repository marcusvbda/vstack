<template>
    <div>
        <template v-if="loading">
            <div class="row d-flex justify-content-end" :style="{ marginTop: 57 }">
                <div class="col-sm-12 col-md-5">
                    <div class="shimmer" :style="{ height: 33, width: '100%' }" />
                </div>
            </div>
            <div class="row d-flex justify-content-end" :style="{ marginTop: 26 }">
                <div class="col-12">
                    <div class="shimmer" :style="{ height: 1000, width: '100%' }" />
                </div>
            </div>
        </template>
        <template v-else>
            <VRuntimeTemplate :template="template" />
        </template>
    </div>
</template>
<script>
import VRuntimeTemplate from "v-runtime-template";

export default {
    props: ["resource_id", "report_mode"],
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
                .post(`/admin/${this.resource_id}/${this.report_mode ? "report" : "list"}/get-list-data`, this.query_params)
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
