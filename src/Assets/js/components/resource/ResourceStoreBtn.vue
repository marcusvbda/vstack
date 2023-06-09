<template>
    <div>
        <button
            id="resource-btn-store"
            class="vstack-btn primary"
            @click.prevent="click"
            v-html="label"
        />
        <resource-crud-dialog
            :resource_id="resource_id"
            ref="dialog"
            :crud_type="crud_type"
        />
    </div>
</template>
<script>
export default {
    props: ['label', 'route', 'crud_type', 'resource_id', 'big'],
    computed: {
        isWizard() {
            return this.crud_type.template == 'wizard';
        },
        isPage() {
            return this.crud_type.template == 'page';
        },
        isDialog() {
            return this.crud_type.template == 'dialog';
        },
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
        },
    },
};
</script>
