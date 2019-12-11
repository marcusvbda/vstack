<template>
    <div class="row d-flex justify-content-center mt-4">
        <div class="col-12">
            <el-steps :active="config.step" finish-status="success" align-center>
                <el-step title="Upload de arquivo"></el-step>
                <el-step title="Mapeamento de coluna"></el-step>
                <el-step title="Importação"></el-step>
            </el-steps>
            <div class="mt-3">
                <a class="link" href="#" v-if="config.step>0" @click.prevent="config.step--">
                    <span class="el-icon-caret-left mr-2 mb-2"></span>
                    Voltar à etapa anterior
                </a>
                <step-0 v-if="config.step==0" :data="data" :frm="frm" :config="config"/>
                <step-1 v-if="config.step==1" :data="data" :frm="frm" :config="config"/>
                <step-2 v-if="config.step>=2" :data="data" :frm="frm" :config="config"/>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props : ['data'],
    data() {
        return {
            frm : {
                file : null
            },
            config : {
                show_advanced : false,
                delimiter : ",",
                step : 0,
                data : {
                    columns : this.data.resource.columns,
                    csv_header : []
                },
                fieldlist : {}
            }
        }
    },
    components : {
        "step-0" : require("./partials/-ImportStep0").default,
        "step-1" : require("./partials/-ImportStep1").default,
        "step-2" : require("./partials/-ImportStep2").default,
    }
}
</script>