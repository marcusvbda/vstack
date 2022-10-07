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
            const el_id = `#loading-section ${el}`;
            this.$waitForEl(el_id).then(() => {
                document.querySelector(el_id).remove();
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
                        this.template.no_data = data.no_data;
                        const template = data.template.join("");
                        this.template.no_data = template;
                    } else {
                        const top_template = data.top.join("");
                        this.template.top = top_template;
                        this.removeLoadingEl("#top-loader")

                        const table_template = data.table.join("");
                        this.template.table = table_template;
                        this.removeLoadingEl("#table-loader")
                    }
                })
                .catch((error) => {
                    console.log(error);
                });
        },
    },
};
</script>
