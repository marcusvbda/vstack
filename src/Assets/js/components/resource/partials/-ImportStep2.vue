<template>
    <div class="vstack-crud-card p-5 flex flex-col items-center justify-center">
        <h1 style="font-size: 150px" class="mt-4 text-green-600">
            <span class="el-icon-info" />
        </h1>
        <h4 class="text-center text-3xl text-neutral-700">
            Importação de Planilha em execução, em breve será finalizado !
        </h4>
        <div class="flex items-center gap-4 flex-col md:flex-row mt-5">
            <button
                :loading="loading"
                class="vstack-btn primary mb-4"
                @click="back"
            >
                Ver {{ data.resource.label.toLowerCase() }}
            </button>
        </div>
    </div>
</template>
<script>
export default {
    props: ['data', 'frm', 'config'],
    methods: {
        back() {
            window.location.href = this.data.resource.route;
        },
    },
};
</script>
