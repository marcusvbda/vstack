<template>
    <el-button
        :icon="icon"
        class="border dark:border-none bg-gray-50 dark:bg-gray-900"
        @click="toggle"
    />
</template>
<script>
export default {
    data() {
        return {
            theme: 'dark',
        };
    },
    computed: {
        icon() {
            return this.theme == 'dark' ? 'el-icon-sunny' : 'el-icon-moon';
        },
    },
    created() {
        this.theme = localStorage.getItem('theme') || 'dark';
    },
    methods: {
        toggle() {
            this.theme = this.theme == 'dark' ? 'light' : 'dark';
            localStorage.setItem('theme', this.theme);
            if (this.theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            this.$emit('themeChange', this.theme);
        },
    },
};
</script>
