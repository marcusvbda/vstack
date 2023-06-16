<template>
    <div
        class="flex flex-wrap gap-3 mt-5"
        id="resource-list-items"
        v-if="!loading"
    >
        <div
            v-for="(list_item, i) in list_items"
            :key="i"
            :class="`${
                list_item.class ? list_item.class : 'w-full md:w-2/12'
            } bg-white rounded p-3 border`"
            :id="`resource-list-item-${list_item.label}`"
        >
            <v-runtime-template
                v-if="list_item.template"
                :key="i"
                :template="list_item.template"
            />
            <div class="list-item--content" v-else>
                <b
                    class="list-item--title text-neutral-700 text-sm"
                    v-html="list_item.label"
                />
                <div
                    class="list-item--value text-neutral-400 text-xs"
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
