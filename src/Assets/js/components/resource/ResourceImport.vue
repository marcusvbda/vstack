<template>
    <div class="row d-flex justify-content-center mt-4" data-aos="fade-left">
        <div class="col-12">
            <el-steps :active="config.step" finish-status="success" align-center>
                <el-step title="Upload de arquivo" />
                <el-step :title="
                    data.resource.import_custom_map_step ? data.resource.import_custom_map_step : 'Mapeamento de coluna'
                " />
                <el-step :title="
                    data.resource.import_custom_import_step ? data.resource.import_custom_import_step : 'Importação'
                " />
            </el-steps>
            <div class="mt-3" data-aos="fade-right">
                <a class="link" href="#" v-if="config.step > 0" @click.prevent="reload">
                    <span class="el-icon-caret-left mr-2 mb-2" />
                    Voltar
                </a>
                <step-0 v-if="config.step == 0" :data="data" :frm="frm" :config="config" />
                <step-1 v-if="config.step == 1" :data="data" :frm="frm" :config="config" />
                <step-2 v-if="config.step >= 2" :data="data" :frm="frm" :config="config" />
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ["data"],
    data() {
        return {
            frm: {
                file: null,
            },
            config: {
                step: 0,
                data: {
                    columns: this.data.resource.columns,
                    csv_header: [],
                },
                fieldlist: {},
            },
        };
    },
    components: {
        "step-0": require("./partials/-ImportStep0").default,
        "step-1": require("./partials/-ImportStep1").default,
        "step-2": require("./partials/-ImportStep2").default,
    },
    methods: {
        reload() {
            window.location.reload();
        },
    },
};
</script>
