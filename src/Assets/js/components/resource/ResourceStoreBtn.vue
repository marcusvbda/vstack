<template>
    <button
        id="resource-btn-store"
        class="vstack-btn primary block"
        @click.prevent="click"
        v-html="label"
    />
</template>
<script>
export default {
    props: ['label', 'route', 'resource_id', 'big'],
    methods: {
        click() {
            return (window.location.href = this.route);
        },
    },
};
</script>
