<template>
    <div
        class="vstack-crud-card p-5"
        v-loading="loading"
        element-loading-text="Aguarde ..."
    >
        <template v-if="data.resource.import_custom_map_step">
            <h3
                class="font-light text-3xl"
                v-if="data.resource.import_custom_map_step.title"
            >
                {{ data.resource.import_custom_map_step.title }}
            </h3>
            <small
                v-if="data.resource.import_custom_map_step.subtitle"
                v-html="data.resource.import_custom_map_step.subtitle"
                class="text-neutral-500 mt-3 w-full"
            />
            <div class="w-full mt-4 flex flex-col">
                <v-runtime-template
                    :template="data.resource.import_custom_map_step.template"
                />
            </div>
        </template>
        <template v-else>
            <h3 class="font-light text-3xl">
                Mapear campos da planilha para
                {{ data.resource.label.toLowerCase() }}
            </h3>
            <small class="text-neutral-500 mt-3 w-full">
                Selecione os campos da sua planilha (a esquerda) e relacione-os
                com os campos que deverão ser importados em
                {{ data.resource.label.toLowerCase() }} (a direita)
            </small>
        </template>
        <table class="mt-5 w-full">
            <thead>
                <tr>
                    <th class="p-4 border text-neutral-700">Nome da coluna</th>
                    <th class="p-4 border">Mapear para o campo</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(column, i) in config.data.csv_header" :key="i">
                    <template>
                        <td class="p-4 font-bold border">
                            {{ column }}
                        </td>
                        <td class="p-4 border">
                            <el-select
                                class="w-full"
                                clearable
                                v-model="config.fieldlist[column]"
                                filterable
                                placeholder="Seleciona para onde este campo será importado"
                            >
                                <el-option label="Ignorar" value="_IGNORE_" />
                                <el-option
                                    v-for="(item, i) in headerOptions"
                                    :key="i"
                                    :label="item"
                                    :value="item"
                                    :disabled="columnHasSelected(item)"
                                />
                            </el-select>
                        </td>
                    </template>
                </tr>
            </tbody>
        </table>
        <div class="w-full flex flex-col md:flex-row justify-end mt-5 pt-4">
            <button
                class="vstack-btn primary"
                :loading="loading"
                @click="submit"
                :disabled="canExecute"
            >
                Executar Importador
            </button>
        </div>
    </div>
</template>
<script>
import VRuntimeTemplate from 'v-runtime-template';

export default {
    props: ['data', 'frm', 'config'],
    data() {
        return {
            loading: false,
            step_data: {
                can_next: false,
            },
        };
    },
    components: {
        'v-runtime-template': VRuntimeTemplate,
    },
    computed: {
        headerOptions() {
            return [].concat(this.config.data.columns);
        },
        canExecute() {
            if (this.data.resource.import_custom_map_step) {
                return !this.step_data.can_next;
            }
            return (
                Object.keys(this.config.fieldlist).length <
                this.config.data.csv_header.length
            );
        },
    },
    mounted() {
        this.config.fieldlist = {};
        this.relateColumns();
    },
    methods: {
        columnHasSelected(item) {
            let headers = this.config.data.csv_header;
            let selected = headers.find((header) => {
                return this.config.fieldlist[header] == item;
            });
            return selected ? true : false;
        },
        relateColumns() {
            let columns = this.headerOptions;
            let headers = this.config.data.csv_header;
            console.log(columns, headers)
            for (let i in headers) {
                let index = columns.indexOf(headers[i]);
                if (index > -1) {
                    this.$set(
                        this.config.fieldlist,
                        columns[index],
                        headers[i]
                    );
                }
            }
        },
        next() {
            this.loading = true;
        },
        makeFormValidationErrors(er) {
            let errors = er.response.data.errors;
            this.errors = errors;
            try {
                let message = Object.keys(errors)
                    .map((key) => `<li>${errors[key][0]}</li>`)
                    .join('');
                this.$message({
                    dangerouslyUseHTMLString: true,
                    showClose: true,
                    message: `<ul>${message}</ul>`,
                    type: 'error',
                });
            } catch {
                return;
            }
        },
        submit() {
            this.loading = true;
            let data = new FormData();

            data.append('file', this.frm.file);
            data.append('config', JSON.stringify(this.config));

            Object.keys(this.frm).forEach((key) => {
                if (!['file', 'config'].includes(key)) {
                    data.append(key, this.frm[key]);
                }
            });

            this.$http
                .post(this.data.resource.route + '/import/submit', data)
                .then((res) => {
                    res = res.data;
                    this.loading = false;
                    if (!res.success)
                        return this.$message({
                            showClose: true,
                            message: res.message,
                            type: 'error',
                        });
                    this.config.step += 2;
                })
                .catch((er) => {
                    console.log(er);
                    this.loading = false;
                    this.makeFormValidationErrors(er);
                });
        },
    },
};
</script>
