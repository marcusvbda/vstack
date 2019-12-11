<template>
    <div :class="`col-md-${metric.width} col-sm-12 mb-3`" style="color: #7c858e;">
        <div class="card d-flex flex-column justify-content-between p-3 h-100 h-100" >
            <div class="d-flex align-items-center" v-if="metric.title || metric.subtitle">
                <div class="w-100 h-100">
                    <div class="d-flex flex-row justify-content-between align-items-center mb-2">
                        <b v-html="metric.title"></b>
                        <b v-html="metric.subtitle" v-if="['custom-content','group-chart'].includes(metric.type)"></b>
                        <template v-if="['trend-counter','trend-chart'].includes(metric.type)">
                            <el-date-picker size='mini' 
                                v-model='filter.range' type='daterange'
                                format="dd/MM/yyyy"
                                value-format='yyyy-MM-dd'
                                end-placeholder='Data Fim'
                                start-placeholder='Data início'>
                            </el-date-picker>
                        </template>
                    </div>
                </div>
            </div>

            <v-runtime-template v-if="metric.type=='custom-content'" :template="`<span>${metric.content}</span>`" />
            <template v-if="metric.type=='trend-counter'">
                <div class="d-flex flex-column justify-content-between" ref="content" style="display:none;">
                    <h2 v-loading="loading">{{value ? value.toFixed(2).replace(/[.,]00$/, "") : 0}}</h2>
                    <div class="mt-3">
                        <span v-html="trend"></span>
                    </div>
                </div>
            </template>
            <template v-if="metric.type=='group-chart'">
                <div class='d-flex flex-row justify-content-between align-items-center h-100' >
                    <div v-html="legend" style="font-size:11px;"></div>
                    <div>
                        <pie-chart :discrete="true" :data="data" :legend='false' :donut='true' 
                        :colors="colors" height='100px' width='100px' />
                    </div>
                </div>
            </template>
            <template v-if="metric.type=='trend-chart'">
                <area-chart :discrete="true" :data="data"  height="120px" />
            </template>
        </div>
    </div>
</template>
<script>
import VRuntimeTemplate from "v-runtime-template"
export default {
    props:["metric","calculate_route"],
    data() {
        return {
            loaded : false,
            data : [],
            filter :{},
            options : [],
            compare : 0,
            value : null
        }
    },
    async created() {
        if(['trend-counter','group-chart','trend-chart'].includes(this.metric.type)) {
            this.initDateInterval()
            this.updateData()
            setInterval(_ => {
                this.updateData()
            },this.metric.update_interval*1000)
        }
    },
    computed : {
        trend() {
            let percent = this.getTrendPercent()
            if(Number(percent)==0) return "<div class='d-flex align-items-center'><h5>Sem Alteração</h5></div>"
            if(Number(this.value)<Number(this.compare)) return `<div class='d-flex align-items-center'><h5>${percent}%<span class='el-icon-bottom-left text-danger ml-2'></span></h5></div>`
            return `<div class='d-flex align-items-center'><h5>${percent}%<span class='el-icon-top-right text-success ml-2'></span></h5></div>`
        },
        legend() {
            let colors = this.colors
            let data = this.data
            let text = "<div class='d-flex flex-column'>"
            let keys = Object.keys(data)
            for(let i in keys)
            {
                text += `<div class="d-flex align-items-center">
                            <div class="mr-2" style="background-color:${colors[i]};height: 12px;width: 25px;margin-top: 2px;"></div>
                            <div>${keys[i]} : ${data[keys[i]]}</div>
                        </div>`
            }
            text+="</div>"
            return text
        },
        colors() {
            let colors = []
            let keys = Object.keys(this.data)
            for (let i in keys) {
                colors.push("#"+this.intToRGB(this.hashCode(keys[i])))
            }
            return colors
        },
    },
    watch : {
        filter: {
            handler(val) {
                if(!this.loaded) return
                this.updateData()
            },
            deep : true
        }
    },
    components : {
        "v-runtime-template" : VRuntimeTemplate
    },
    methods : {
        hashCode(str) { 
            var hash = 0
            for (var i = 0; i < str.length; i++) hash = str.charCodeAt(i) + ((hash << 5) - hash)
            return hash
        },
        intToRGB(i){
            var c = (i & 0x00FFFFFF).toString(16).toUpperCase()
            return "00000".substring(0, 6 - c.length) + c
        },
        getTrendPercent() {
            let compare = Number(this.compare)
            let value = compare-Number(this.value)
            if(value<=0) return (value*-1).toFixed(2).replace(/[.,]00$/, "")
            let result = ((value*100)/compare)
            if(result<0) result*=-1
            return result.toFixed(2).replace(/[.,]00$/, "")
        },
        initDateInterval() {
            let endDate = new Date()
            let startDate = new Date(new Date().setDate(endDate.getDate()-15))
            this.$set(this.filter,"range",[startDate.toISOString().slice(0, 10),endDate.toISOString().slice(0, 10)])
        },
        updateData() {
            if(!this.filter.range) return
            this.loading = true
            this.$http.post(this.calculate_route,this.filter).then( res => {
                this.data = res.data ? res.data : []
                this.value = res.data.value ? res.data.value : 0
                this.compare = res.data.compare ? res.data.compare : 0
                this.loading = false
                $(this.$refs.content).show()
                this.loaded = true
            }).catch(er => {
                console.log(er)
                this.loading = false
            })
        }
    }
}
</script>