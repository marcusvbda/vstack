<template>
    <el-tooltip class="item" effect="dark" content="Clique para copiar o conteúdo completo" placement="top">
        <a :href="value" @click.prevent="copy" v-html="text ? text : value" />
    </el-tooltip>
</template>
<script>
export default {
    props: {
        text: { type: String, default: "" },
        value: { type: String, default: "" },
        message: { type: String, default: "Texto copiado para o clipboard" },
    },
    methods: {
        copy() {
            let str = this.value ? this.value : "";
            str.toClipboard(() => {
                if (!this.message) return;
                this.$message({
                    message: this.message,
                    type: "info",
                });
            });
        },
    },
};
</script>
