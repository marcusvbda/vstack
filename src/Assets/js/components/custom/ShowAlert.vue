<template></template>
<script>
export default {
    props: ['messages'],
    created() {
        let showAbleMessages = this.messages.filter(
            (row) =>
                typeof row?.message == 'string' && typeof row?.type == 'string'
        );
        let lastMessage = showAbleMessages[showAbleMessages.length - 1];
        this.$message({
            dangerouslyUseHTMLString: true,
            showClose: true,
            message: lastMessage.message,
            type: lastMessage.type,
        });
    },
};
</script>
