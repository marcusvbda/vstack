<template>
    <div>
        <template v-if="loading">
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
            <div class="row d-flex justify-content-start" :style="{ marginTop: 26 }">
                <div class="col-md-2 col-sm-12">
                    <div class="shimmer" :style="{ height: 70, width: '100%' }" />
                </div>
                <div class="col-md-3 col-sm-12">
                    <div class="shimmer" :style="{ height: 70, width: '100%' }" />
                </div>
            </div>
            <div class="row d-flex justify-content-end" :style="{ marginTop: 26 }">
                <div class="col-12">
                    <div
                        class="shimmer"
                        :style="{ height: 450, width: '100%' }"
                        v-loading="true"
                        element-loading-text="Aguarde ..."
                        element-loading-spinner="el-icon-loading"
                    />
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
// import io from "socket.io-client";

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
        requestForData(params) {
            const route = `/admin/${this.resource_id}/${this.report_mode ? "report" : "list"}/get-list-data`;
            this.$http
                .post(route, params)
                .then(({ data }) => {
                    this.loading = false;
                    this.template = data.template_chunked.join("");
                })
                .catch((error) => {
                    console.log(error);
                    this.loading = false;
                });
        },
        init() {
            this.loading = true;
            // if (laravel.chat.enabled) {
            //     const route = `${laravel.chat.uri}:${laravel.chat.port}`;
            //     const socket = io(route);
            //     socket.on("connected", (client) => {
            //         this.requestForData({ ...this.query_params, socket_client_id: client.id });
            //     });
            // } else {
            this.requestForData(this.query_params);
            // }
        },
    },
};
</script>
