<template>
    <div
        v-loading="resource_list_is_loading"
        element-loading-text="Aguarde ..."
    >
        <slot />
    </div>
</template>
<script>
import { mapGetters } from 'vuex';

export default {
    computed: {
        ...mapGetters('resource', ['resource_list_is_loading']),
    },
};
</script>
