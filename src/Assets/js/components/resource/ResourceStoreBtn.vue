<template>
    <div>
        <button
            id="resource-btn-store"
            class="vstack-btn primary"
            @click.prevent="click"
            v-html="label"
        />
    </div>
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
