<template>
    <div>
        <a
           id="resource-btn-store"
            :class="`btn btn-primary ${big == undefined ? 'btn-sm' : ''} btn-sm-block cursor-pointer px-3 pr-2 mx-4 mb-1`"
            @click.prevent="click"
            :href="route"
        >
            <div v-html="label" />
        </a>
        <resource-crud-dialog :resource_id="resource_id" ref="dialog" :crud_type="crud_type" />
    </div>
</template>
<script>
export default {
    props: ["label", "route", "crud_type", "resource_id", "big"],
    computed: {
        isWizard() {
            return this.crud_type.template == "wizard";
        },
        isPage() {
            return this.crud_type.template == "page";
        },
        isDialog() {
            return this.crud_type.template == "dialog";
        }
    },
    methods: {
        click() {
            if (this.isPage || this.isWizard) {
                return (window.location.href = this.route);
            }
            if (this.isDialog) {
                return this.openDialog();
            }
        },
        openDialog() {
            this.$refs.dialog.open();
        }
    }
};
</script>
