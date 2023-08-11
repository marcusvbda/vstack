<template>
    <div>
        <VRuntimeTemplate
            v-if="resource_list_template.top"
            :template="resource_list_template.top"
        />
        <slot />
        <VRuntimeTemplate
            v-if="resource_list_template.table"
            :template="resource_list_template.table"
        />
        <VRuntimeTemplate
            v-if="resource_list_template.no_data"
            :template="resource_list_template.no_data"
        />
    </div>
</template>
<script>
import VRuntimeTemplate from 'v-runtime-template';
import { mapMutations, mapActions, mapGetters } from 'vuex';

export default {
    props: [
        'resource_id',
        'report_mode',
        'cursor',
        'only_table',
        'extra_filters',
        'related_resource',
        'related_resource_id',
        'raw_type',
        'hash',
    ],
    components: {
        VRuntimeTemplate,
    },
    computed: {
        ...mapGetters('resource', [
            'resource_list_template',
            'resource_list_payload',
        ]),
        query_params() {
            return this.$getUrlParams();
        },
    },
    created() {
        const payload = {
            params: {
                ...this.query_params,
                list_type: this.only_table ? 'table' : 'full',
                related_resource: this.related_resource,
                related_resource_id: this.related_resource_id,
                raw_type: this.raw_type,
                hash: this.hash,
            },
        };
        if (this.extra_filters) {
            payload.params = {
                ...payload.params,
                ...this.extra_filters,
            };
        }

        this.setCursor(this.cursor);
        this.setReportMode(this.report_mode);
        this.setResourceListId(this.resource_id);
        this.setResourceListPayload(payload);
        this.loadResourceData();
    },
    methods: {
        ...mapMutations('resource', [
            'setResourceListPayload',
            'setResourceListId',
            'setReportMode',
            'setCursor',
        ]),
        ...mapActions('resource', ['loadResourceData']),
        removeLoadingEl(el) {
            this.$waitForEl(el).then(() => {
                document.querySelector(el).remove();
            });
        },
    },
};
</script>
