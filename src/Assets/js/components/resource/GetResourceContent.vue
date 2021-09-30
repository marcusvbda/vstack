<template>
    <tr :id="row_id">
        <template v-if="loading">
            <td :colspan="colspan">
                <div
                    class="shimmer"
                    :style="{
                        width: '100%',
                        height: Math.random() * (45 - 10) + 20
                    }"
                />
            </td>
        </template>
        <template v-else>
            <slot name="first-column" />
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
            <td v-if="show_right_actions_column">
                <resource-crud-buttons
                    :resource_id="resource_id"
                    :data="{
                        code: row_code,
                        route: `${resource_route}/${row_code}`,
                        can_view: can_view,
                        can_update: can_update,
                        can_delete: can_delete,
                        can_clone: can_clone,
                        before_delete: before_delete,
                        crud_type: crud_type,
                        resource_label: resource_label,
                        resource_icon: resource_icon,
                        resource_singular_label: resource_singular_label
                    }"
                    :id="row_id"
                />
            </td>
        </template>
    </tr>
</template>
<script>
import VRuntimeTemplate from "v-runtime-template";
export default {
    components: { "v-runtime-template": VRuntimeTemplate },
    props: [
        "type",
        "row_code",
        "resource_route",
        "cols",
        "row_id",
        "resource_id",
        "raw_content",
        "show_right_actions_column",
        "has_actions"
    ],
    data() {
        return {
            loading: true,
            content: {},
            can_clone: false,
            can_update: false,
            can_delete: false,
            crud_type: "page",
            before_delete: [],
            resource_singular_label: "",
            resource_icon: "",
            resource_label: "",
            before_clone: [],
            can_view: false,
            pusher_initialized: false
        };
    },
    created() {
        this.getContent();
    },
    computed: {
        colspan() {
            let cols = this.cols;
            if (this.show_right_actions_column) {
                cols++;
            }
            if (this.$slots["first-column"]) {
                cols++;
            }
            if (this.has_actions) {
                cols++;
            }
            return cols;
        }
    },
    methods: {
        getContent() {
            switch (this.type) {
            case "resourceTableContent":
                return this.getResourceTableContent();
            case "resourceTableIndex":
                return this.getResourceTableIndex();
            }
        },
        getResourceTableIndex() {
            this.$http
                .post(
                    `/vstack/${this.resource_id}/get-partial-content`,
                    {
                        type: this.type,
                        raw_content: this.raw_content
                    },
                    { retries: 3 }
                )
                .then(resp => {
                    resp = resp.data;
                    this.content = resp.html;
                    this.loading = false;
                    if (!this.pusher_initialized) {
                        this.initPusher();
                        this.pusher_initialized = true;
                    }
                });
        },
        getResourceTableContent() {
            this.$http
                .post(
                    `/vstack/${this.resource_id}/get-partial-content`,
                    {
                        row_id: this.row_id,
                        type: this.type,
                        raw_content: this.raw_content
                    },
                    { retries: 3 }
                )
                .then(resp => {
                    resp = resp.data;
                    this.content = resp.content;
                    Object.keys(resp.acl).forEach(key => (this[key] = resp.acl[key]));
                    this.loading = false;
                    if (!this.pusher_initialized) {
                        this.initPusher();
                        this.pusher_initialized = true;
                    }
                });
        },
        initPusher() {
            if (laravel.tenant.id && laravel.chat.pusher_key) {
                this.$echo
                    .private(`App.Tenant.${laravel.tenant.id}`)
                    .listen(`.notifications.resource.${this.resource_id}.${this.row_id}`, resp => {
                        if (resp.event == "reload") {
                            this.getResourceTableContent();
                        }
                    });
            }
        }
    }
};
</script>
