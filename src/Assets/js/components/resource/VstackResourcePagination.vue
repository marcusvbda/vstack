<template>
    <el-pagination
        v-if="resource_list_total"
        background
        layout="total, prev, pager, next"
        :total="resource_list_total"
        :page-size="per_page"
        :current-page.sync="page"
    />
</template>
<script>
import { mapGetters, mapMutations } from 'vuex';

export default {
    props: ['per_page', 'current_page'],
    data() {
        return {
            page: this.current_page,
        };
    },
    computed: {
        ...mapGetters('resource', ['resource_list_total']),
    },
    methods: {
        ...mapMutations('resource', ['setResourceListTotal']),
    },
};
</script>
