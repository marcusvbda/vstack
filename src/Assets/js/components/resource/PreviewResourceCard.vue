<template>
    <div :class="`col-md-${metric.width ? metric.width : '4'} col-sm-12 px-0`">
        <div class="card p-3 h-100">
            <div class="h-100">
                <div class="d-flex flex-row justify-content-between align-items-center mb-2">
                    <b v-html="metric.title"></b>
                    <b v-if="['custom-content'].includes(metric.type)" v-html="metric.subtitle"></b>
                    <template v-else>
                        <el-date-picker size='mini' 
                            type='daterange'
                            format="dd/MM/yyyy"
                            value-format='yyyy-MM-dd'
                            end-placeholder='Data Fim'
                            start-placeholder='Data início'
                            disabled
                        />
                    </template>
                </div> 
            </div>
            <div v-if="metric.type=='custom-content'" v-html="metric.content"></div>
            <template v-else>
                <h2>15</h2>
                <div class="mt-3">
                    <div class="d-flex align-items-center">
                        <h5>
                            78.3%<span class="el-icon-top-right text-success ml-2"></span>
                        </h5>
                    </div>
                </div>
            </template>
        </div>
        <small class="float-right">Pré-Visualização Ilustrativa</small>
    </div>
</template>
<script>
export default {
    props :["metric"]
}
</script>