<template>
    <div>
        <VRuntimeTemplate v-if="template.top" :template="template.top" />
        <VRuntimeTemplate v-if="template.table" :template="template.table" />
        <VRuntimeTemplate v-if="template.no_data" :template="template.no_data" />
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
            template: {
                no_data: "",
                top: "",
                table: "",
            },
        };
    },
    computed: {
        query_params() {
            return this.$getUrlParams();
        },
    },
    created() {
        this.init();
    },
    methods: {
        removeLoadingEl(el) {
            this.$waitForEl(el).then(() => {
                document.querySelector(el).remove();
            })
        },
        init() {
            const payload = {
                params: this.query_params
            };
            const route = `/admin/${this.resource_id}/${this.report_mode ? "report" : "list"}/get-list-data`;
            this.$http
                .get(route, payload)
                .then(({ data }) => {
                    if (data.type == "no_data") {
                        const no_data_template = data.template.join("");
                        this.template.no_data = no_data_template;
                        this.removeLoadingEl("#loading-section")
                    } else {
                        const top_template = data.top.join("");
                        this.template.top = top_template;
                        this.removeLoadingEl("#loading-section #top-loader")

                        const table_template = data.table.join("");
                        this.template.table = table_template;
                        this.removeLoadingEl("#loading-section #table-loader")
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        },
    },
};
</script>
