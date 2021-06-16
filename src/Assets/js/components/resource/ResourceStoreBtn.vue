<template>
    <div>
        <a :class="`btn btn-primary ${big == undefined ? 'btn-sm' : ''} btn-sm-block cursor-pointer px-3 pr-2 mx-4 mb-1`" @click.prevent="click" :href="route">
            <div v-html="label" />
        </a>
        <resource-crud-dialog :resource_id="resource_id" ref="dialog" />
    </div>
</template>
<script>
export default {
    props: ['label', 'route', 'crud_type', 'resource_id', 'big'],
    computed: {
        isPage() {
            return this.crud_type == 'page'
        },
        isDialog() {
            return this.crud_type == 'dialog'
        },
    },
    methods: {
        click() {
            if (this.isPage) {
                return (window.location.href = this.route)
            }
            this.openDialog()
        },
        openDialog() {
            this.$refs.dialog.open()
        },
    },
}
</script>
