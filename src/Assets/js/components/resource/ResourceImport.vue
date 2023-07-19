<template>
    <div class="flex flex-col justify-center my-4" style="margin-bottom: 150px">
        <el-steps :active="config.step" finish-status="success" align-center>
            <el-step title="Upload de arquivo" />
            <el-step
                :title="
                    data.resource.import_custom_map_step
                        ? data.resource.import_custom_map_step.title
                        : 'Mapeamento de coluna'
                "
            />
            <el-step
                :title="
                    data.resource.import_custom_import_step
                        ? data.resource.import_custom_import_step
                        : 'Importação'
                "
            />
        </el-steps>
        <div class="mt-3">
            <a
                class="link vstack-link"
                href="#"
                v-if="config.step > 0"
                @click.prevent="reload"
            >
                <span class="el-icon-caret-left mr-2 mb-2" />
                Voltar
            </a>
            <step-0
                v-if="config.step == 0"
                :data="data"
                :frm="frm"
                :config="config"
            />
            <step-1
                v-if="config.step == 1"
                :data="data"
                :frm="frm"
                :config="config"
            />
            <step-2
                v-if="config.step >= 2"
                :data="data"
                :frm="frm"
                :config="config"
            />
        </div>
    </div>
</template>
<script>
import ImportStep0 from './partials/-ImportStep0';
import ImportStep1 from './partials/-ImportStep1';
import ImportStep2 from './partials/-ImportStep2';

export default {
    props: ['data'],
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
        'step-0': ImportStep0,
        'step-1': ImportStep1,
        'step-2': ImportStep2,
    },
    methods: {
        reload() {
            window.location.reload();
        },
    },
};
</script>
