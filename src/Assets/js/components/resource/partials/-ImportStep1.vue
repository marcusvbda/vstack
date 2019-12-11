<template>
    <div class="card">
        <div class="card-header bg-white py-4">
            <div class="row">
                <div class="col-12">
                    <h3 class="font-weight-light">Mapear campos do CSV para produtos {{data.resource.label.toLowerCase()}}</h3>
                    <div class="mt-3">Selecione os campos de seu arquivo CSV (a esquerda) e relacione-os com os campos que deverão ser importados em {{data.resource.label.toLowerCase()}} (a direita)</div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="row">
                <div class="col-12">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th class="px-5">Nome da coluna</th>
                                <th class="px-5">Mapear para o campo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="column in config.data.csv_header">
                                <td class="px-5">{{column}}</td>
                                <td class="px-5">
                                    <el-select class="w-100" clearable v-model="config.fieldlist[column]" filterable placeholder="Seleciona para onde este campo será importado">
                                        <el-option label="Não Importar" value="_IGNORE_"></el-option>
                                        <el-option v-for="(item,i) in config.data.columns" :key="i" :label="item" :value="item"></el-option>
                                    </el-select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer bg-white">
            <div class="row">
                <div class="col-12 d-flex flex-row flex-wrap align-items-center justify-content-end">
                    <button class="btn btn-primary" @click="next" :disabled="Object.keys(config.fieldlist).length<config.data.csv_header.length" >Executar Importador</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props:["data","frm","config"],
    methods :{
        next() {
            this.loading = true
            this.config.step+=2
        },
    },
    mounted() {
        this.config.fieldlist = {}
    }
}
</script>