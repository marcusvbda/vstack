<template>
    <div class="row list-items" id="resource-list-items">
        <template v-if="loading">
            <div
                class="col-md-2 col-sm-12"
                :style="{ marginTop: 26, marginBottom: 18 }"
            >
                <div class="shimmer" :style="{ height: 75, width: '100%' }" />
            </div>
            <div
                class="col-md-3 col-sm-12"
                :style="{ marginTop: 26, marginBottom: 18 }"
            >
                <div class="shimmer" :style="{ height: 75, width: '100%' }" />
            </div>
        </template>
        <div
            v-else
            v-for="(list_item, i) in list_items"
            :key="i"
            :class="`col list-item ${list_item.class ? list_item.class : ''}`"
            :id="`resource-list-item-${list_item.label}`"
        >
            <v-runtime-template
                v-if="list_item.template"
                :key="i"
                :template="list_item.template"
            />
            <div class="list-item--content" v-else>
                <b class="list-item--title" v-html="list_item.label" />
                <div
                    class="list-item--value text-neutral-400"
                    v-html="list_item.value"
                />
            </div>
        </div>
    </div>
</template>
<script>
import VRuntimeTemplate from 'v-runtime-template';

export default {
    props: ['resource_id', 'request_data'],
    data() {
        return {
            loading: true,
            list_item: [],
        };
    },
    components: {
        'v-runtime-template': VRuntimeTemplate,
    },
    created() {
        this.getValues();
    },
    methods: {
        getValues() {
            this.$http
                .post(`/admin/get-list-cards`, {
                    ...this.request_data,
                    resource_id: this.resource_id,
                })
                .then(({ data }) => {
                    this.list_items = data;
                    this.loading = false;
                });
        },
    },
};
</script>
