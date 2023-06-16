<template>
    <div class="flex items-center gap-3 pb-3">
        <div class="flex items-center w-full justify-center md:justify-end">
            <div class="flex flex-col items-end gap-2">
                <div class="flex flex-col md:flex-row items-center gap-3">
                    <span
                        v-html="totalText"
                        v-if="resource_list_total"
                        class="text-neutral-400 text-xs"
                    />
                    <div class="flex gap-1">
                        <el-button
                            type="primary"
                            icon="el-icon-arrow-left"
                            plain
                            size="small"
                            :disabled="!resource_list_previous_cursor"
                            v-if="showContent"
                            @click="goToPrevPage()"
                            :loading="previous_loading"
                        >
                            Anterior
                        </el-button>
                        <el-button
                            plain
                            type="primary"
                            size="small"
                            :disabled="!resource_list_next_cursor"
                            @click="goToNextPage()"
                            v-if="showContent"
                            :loading="next_loading"
                        >
                            Próxima
                            <i class="el-icon-arrow-right el-icon-right"> </i>
                        </el-button>
                    </div>
                </div>
                <span v-if="showContent" class="text-neutral-400 text-xs">
                    {{ pagesText }}
                </span>
            </div>
        </div>
    </div>
</template>
<script>
import { mapActions, mapGetters, mapMutations } from 'vuex';

export default {
    props: ['appends', 'previous_cursor', 'next_cursor'],
    computed: {
        ...mapGetters('resource', [
            'resource_list_total',
            'resource_list_current_page',
            'resource_list_per_page',
            'resource_list_per_page',
            'resource_list_next_cursor',
            'resource_list_previous_cursor',
            'resource_total_text',
            'previous_loading',
            'next_loading',
        ]),
        showContent() {
            return (
                this.resource_list_total &&
                this.resource_list_current_page &&
                this.resource_list_per_page &&
                this.pagesText != 'Página 1 de 1'
            );
        },
        totalPages() {
            return Math.ceil(
                this.resource_list_total / this.resource_list_per_page
            );
        },
        pagesText() {
            return `Página ${this.resource_list_current_page} de ${this.totalPages}`;
        },
        totalText() {
            return `${this.resource_total_text} ${this.resource_list_total}`;
        },
    },
    created() {
        this.setResourceListPreviousCursor(this.previous_cursor);
        this.setResourceListNextCursor(this.next_cursor);
    },
    methods: {
        ...mapActions('resource', ['loadResourceData']),
        ...mapMutations('resource', [
            'setResourceListPreviousCursor',
            'setResourceListNextCursor',
            'setResourceListCurrentPage',
            'setNextLoading',
            'setPreviousLoading',
        ]),
        goToNextPage() {
            this.setNextLoading(true);
            this.loadResourceData({
                value: this.resource_list_next_cursor,
                callback: () => {
                    this.setResourceListCurrentPage(
                        this.resource_list_current_page + 1
                    );
                    this.setNextLoading(false);
                },
            });
        },
        goToPrevPage() {
            this.setPreviousLoading(true);
            this.loadResourceData({
                value: this.resource_list_previous_cursor,
                callback: () => {
                    this.setResourceListCurrentPage(
                        this.resource_list_current_page - 1
                    );
                    this.setPreviousLoading(false);
                },
            });
        },
    },
};
</script>