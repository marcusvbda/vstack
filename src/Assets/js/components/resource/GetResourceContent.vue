<template>
    <tr :id="row_id">
        <slot name="first-column" />
        <template v-if="loading">
            <template v-for="(item, i) in cols">
                <td :key="i">
                    <div
                        class="shimmer"
                        :style="{
                            width: '100%',
                            height: Math.random() * (45 - 10) + 20,
                        }"
                    />
                </td>
            </template>
            <td>
                <div
                    class="shimmer"
                    :style="{
                        width: '100%',
                        height: Math.random() * (45 - 10) + 20,
                    }"
                />
            </td>
        </template>
        <template v-else>
            <td :key="i" v-for="(key, i) in Object.keys(content)">
                <div class="d-flex flex-column">
                    <template v-if="i == 0">
                        <b>
                            <a :href="`${resource_route}/${row_code}`" class="link" v-html="content[key]" v-if="can_view" />
                            <span v-else v-html="content[key]" />
                        </b>
                    </template>
                    <template v-else>
                        <v-runtime-template :template="`<span>${content[key]}</span>`" />
                    </template>
                </div>
            </td>
            <td>
                <resource-crud-buttons
                    :data="{
                        code: row_code,
                        route: `${resource_route}/${row_code}`,
                        can_view: can_view,
                        can_update: can_update,
                        can_delete: can_delete,
                        can_clone: can_clone,
                        before_delete: before_delete,
                    }"
                    :id="row_id"
                />
            </td>
        </template>
    </tr>
</template>
<script>
import VRuntimeTemplate from 'v-runtime-template'
export default {
    components: { 'v-runtime-template': VRuntimeTemplate },
    props: ['type', 'row_code', 'resource_route', 'cols', 'row_id', 'type', 'resource_id'],
    data() {
        return {
            loading: true,
            content: {},
            can_clone: false,
            can_update: false,
            can_delete: false,
            before_delete: [],
            can_view: false,
            pusher_initialized: false,
        }
    },
    created() {
        this.getContent()
    },
    methods: {
        getContent() {
            switch (this.type) {
                case 'resourceTableContent':
                    return this.getResourceTableContent()
                    break
                case 'resourceTableIndex':
                    return this.getResourceTableIndex()
                    break
            }
        },
        getResourceTableIndex() {
            this.$http
                .post(
                    `/vstack/${this.resource_id}/get-partial-content`,
                    {
                        type: this.type,
                    },
                    { retries: 3 }
                )
                .then((resp) => {
                    resp = resp.data
                    this.content = resp.html
                    this.loading = false
                    if (!this.pusher_initialized) {
                        this.initPusher()
                        this.pusher_initialized = true
                    }
                })
        },
        getResourceTableContent() {
            this.$http
                .post(
                    `/vstack/${this.resource_id}/get-partial-content`,
                    {
                        row_id: this.row_id,
                        type: this.type,
                    },
                    { retries: 3 }
                )
                .then((resp) => {
                    resp = resp.data
                    this.content = resp.content
                    Object.keys(resp.acl).forEach((key) => (this[key] = resp.acl[key]))
                    this.loading = false
                    if (!this.pusher_initialized) {
                        this.initPusher()
                        this.pusher_initialized = true
                    }
                })
        },
        initPusher() {
            if (laravel.tenant.id && laravel.chat.pusher_key) {
                this.$echo.private(`App.Tenant.${laravel.tenant.id}`).listen(`.notifications.resource.${this.resource_id}.${this.row_id}`, (resp) => {
                    if (resp.event == 'reload') {
                        this.getResourceTableContent()
                    }
                })
            }
        },
    },
}
</script>
