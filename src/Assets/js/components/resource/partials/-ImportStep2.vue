<template>
    <div class="card">
        <div class="card-body p-0">
            <div class="row">
                <div class="col-12 d-flex flex-column justify-content-center align-items-center my-5">
                    <h1 style="font-size: 150px" class="text-info">
                        <span class="el-icon-info" />
                    </h1>
                    <h4>Importação de Planilha em execução, em breve será finalizado !</h4>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="row">
                <div class="col-12 d-flex flex-row flex-wrap align-items-center justify-content-end">
                    <el-button type="primary" :loading="loading" class="btn btn-primary" @click="back">Ver {{
        data.resource.label.toLowerCase()
                        }}</el-button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ["data", "frm", "config"],
    methods: {
        back() {
            window.location.href = this.data.resource.route
        }
    }
};
</script>
