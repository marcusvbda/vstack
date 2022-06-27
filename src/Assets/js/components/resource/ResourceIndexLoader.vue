<template>
    <div>
        <template v-if="(!loading.top && !loading.table) || !template.no_data">
            <template v-if="loading.top">
                <div class="row d-flex justify-content-start" :style="{ marginTop: 26 }">
                    <div class="col-sm-12 col-md-5 d-flex flex-row" style="gap: 11px">
                        <div class="shimmer" :style="{ height: 19, width: 170 }" />
                        <div class="shimmer" :style="{ height: 19, width: 150 }" />
                        <div class="shimmer" :style="{ height: 19, width: 180 }" />
                    </div>
                </div>
                <div class="row d-flex justify-content-start" :style="{ marginTop: 26 }">
                    <div class="col-sm-12 col-md-5 d-flex flex-row" style="gap: 11px">
                        <div class="shimmer" :style="{ height: 41, width: 100 }" />
                        <div class="shimmer" :style="{ height: 41, width: 120 }" />
                    </div>
                </div>
                <div class="row d-flex justify-content-end" :style="{ marginTop: 26 }">
                    <div class="col-sm-12 col-md-2 d-flex align-items-end">
                        <div class="shimmer" :style="{ height: 17, width: '100%' }" />
                    </div>
                    <div class="col-sm-12 col-md-3">
                        <div class="shimmer" :style="{ height: 33, width: '100%' }" />
                    </div>
                </div>
                <div class="row d-flex justify-content-start" :style="{ marginTop: 26, marginBottom: 18 }">
                    <div class="col-md-2 col-sm-12">
                        <div class="shimmer" :style="{ height: 70, width: '100%' }" />
                    </div>
                    <div class="col-md-3 col-sm-12">
                        <div class="shimmer" :style="{ height: 70, width: '100%' }" />
                    </div>
                </div>
            </template>
            <template v-else>
                <VRuntimeTemplate v-if="template.top" :template="template.top" />
            </template>
            <div class="row d-flex justify-content-end" :style="{ marginTop: 18 }" v-if="loading.table">
                <div class="col-12">
                    <div class="shimmer" :style="{ height: 450, width: '100%' }" />
                </div>
            </div>
            <template v-else>
                <VRuntimeTemplate v-if="template.table" :template="template.table" />
            </template>
        </template>
        <template v-else>
            <VRuntimeTemplate v-if="template.no_data" :template="template.no_data" />
        </template>
    </div>
</template>
<script>
import VRuntimeTemplate from "v-runtime-template";
import io from "socket.io-client";

export default {
    props: ["resource_id", "report_mode"],
    components: {
        VRuntimeTemplate,
    },
    data() {
        return {
            loading: {
                top: true,
                table: true,
                no_data: true,
            },
            template: {
                no_data: "",
                top: "",
            },
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
            this.requestFor;
            const params = this.query_params;
            const route = `/admin/${this.resource_id}/${this.report_mode ? "report" : "list"}/get-list-data`;
            this.$http
                .post(route, params)
                .then(({ data }) => {
                    if (data.type == "no_data") {
                        this.template.no_data = data.no_data;
                        const template = data.template.join("");
                        this.template.no_data = template;
                    } else {
                        const top_template = data.top.join("");
                        this.template.top = top_template;
                        this.loading.top = false;

                        const table_template = data.table.join("");
                        this.template.table = table_template;
                        this.loading.table = false;
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        },
    },
};
</script>
