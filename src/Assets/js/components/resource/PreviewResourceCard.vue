<template>
    <div :class="`col-md-${metric.width ? metric.width : '4'} col-sm-12 px-0`">
        <div class="card p-3 h-100">
            <div class="h-100">
                <div class="d-flex flex-row justify-content-between align-items-center mb-2">
                    <b v-html="metric.title"></b>
                    <b v-if="['custom-content','group-chart'].includes(metric.type)" v-html="metric.subtitle"></b>
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
            <template v-if="metric.type=='trend-counter'">
                <h2>15</h2>
                <div class="mt-3">
                    <div class="d-flex align-items-center">
                        <h5>
                            78.3%<span class="el-icon-top-right text-success ml-2"></span>
                        </h5>
                    </div>
                </div>
            </template>
            <template v-if="metric.type=='group-chart'">
                <div class='d-flex flex-row justify-content-between align-items-center'  >
                    <div style="font-size:11px;">
                        <div class="d-flex flex-column">
                            <div class="d-flex align-items-center">
                                <div class="mr-2" style="background-color:#0b50da;height: 12px;width: 25px;margin-top: 2px;"></div>
                                <div>Lorem : 10</div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="mr-2" style="background-color:#D62B00;height: 12px;width: 25px;margin-top: 2px;"></div>
                                <div>Ipsum : 5</div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <pie-chart :discrete="true" :data="{'lorem' : 10, 'ipsum' :5}" :legend='false' :donut='true' height='100px' width='100px' />
                    </div>
                </div>
            </template>
            <template v-if="metric.type=='trend-chart'">
                <area-chart :discrete="true" :data="{'lorem' : 10, 'ipsum' :5}"  height="120px" />
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