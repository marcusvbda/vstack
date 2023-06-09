<template>
    <div>
        <VRuntimeTemplate v-if="template.top" :template="template.top" />
        <VRuntimeTemplate v-if="template.table" :template="template.table" />
        <VRuntimeTemplate
            v-if="template.no_data"
            :template="template.no_data"
        />
        <portal to="total-count">
            {{ total_count }}
        </portal>
    </div>
</template>
<script>
import VRuntimeTemplate from 'v-runtime-template';

export default {
    props: ['resource_id', 'report_mode'],
    components: {
        VRuntimeTemplate,
    },
    data() {
        return {
            template: {
                no_data: '',
                top: '',
                table: '',
            },
            total_count: '',
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
            });
        },
        init() {
            const payload = {
                params: this.query_params,
            };

            const route = `/admin/${this.resource_id}/${
                this.report_mode ? 'report' : 'list'
            }/get-list-data`;
            this.$http
                .get(route, payload)
                .then(({ data }) => {
                    const top_template = data.top.join('');
                    this.template.top = top_template;
                    this.removeLoadingEl('#loading-section #top-loader');

                    const table_template = data.table.join('');
                    this.template.table = table_template;
                    this.removeLoadingEl('#loading-section #table-loader');
                })
                .catch((error) => {
                    console.log(error);
                });

            const count_route = `/admin/${this.resource_id}/count/get-list-data`;
            this.$http
                .get(count_route, payload)
                .then(({ data }) => {
                    this.total_count = data.count;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
    },
};
</script>
