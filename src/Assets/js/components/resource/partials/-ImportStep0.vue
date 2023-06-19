<template>
    <div
        class="vstack-crud-card p-5"
        v-loading="loading"
        element-loading-text="Aguarde ..."
    >
        <div class="flex flex-col w-full">
            <h3
                class="font-light text-3xl"
                v-if="data.resource.import_settings.page_title"
            >
                {{ data.resource.import_settings.page_title }}
            </h3>
            <small
                v-if="data.resource.import_settings.description"
                v-html="data.resource.import_settings.description"
                class="text-neutral-500 mt-3 w-full"
            />
            <small
                v-if="data.resource.import_custom_crud_message"
                v-html="data.resource.import_custom_crud_message"
                class="text-neutral-500 mt-3 w-full"
            />
        </div>
        <div class="w-full flex items-center mt-4 gap-4">
            <small
                class="w-full md:w-6/12 text-netral-700"
                v-if="data.resource.import_settings.input_text"
                v-html="data.resource.import_settings.input_text"
            />
            <div
                class="w-full mt-4 md:w-6/12 flex-col md:flex-row text-netral-700 gap-4"
            >
                <template v-if="!frm.file">
                    <input
                        accept=".xls, .xlsx"
                        type="file"
                        @change="fileChange"
                    />
                    <div>
                        <small class="mt-2">Tamanho máximo: 128 MB</small>
                    </div>
                </template>
                <template v-else>
                    <div class="flex items-center gap-4 flex-col md:flex-row">
                        <small class="text-neutral-500">
                            {{ frm.file.name }}
                        </small>
                        <a
                            href="#"
                            class="ml-2 link text-danger"
                            @click.prevent="frm.file = null"
                        >
                            Trocar de Arquivo
                        </a>
                    </div>
                </template>
            </div>
        </div>
        <div class="flex mt-3 justify-end">
            <button
                class="vstack-btn primary"
                @click="next"
                :disabled="!frm.file"
            >
                Continuar
            </button>
        </div>
    </div>
</template>
<script>
export default {
    props: ['data', 'frm', 'config'],
    data() {
        return {
            loading: false,
        };
    },
    methods: {
        fileChange(e) {
            var files = e.target.files || e.dataTransfer.files;
            if (!files.length) {
                return;
            }
            this.frm.file = files[0];
        },
        next() {
            this.loading = true;
            let data = new FormData();
            data.append('file', this.frm.file);
            data.append('delimiter', this.config.delimiter);
            this.$http
                .post(this.data.resource.route + '/import/check_file', data)
                .then((res) => {
                    res = res.data;
                    this.loading = false;
                    this.config.data.csv_header = res;
                    this.config.step++;
                })
                .catch((er) => {
                    console.log(er);
                    this.loading = false;
                });
        },
    },
};
</script>
