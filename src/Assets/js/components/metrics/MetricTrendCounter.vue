<template>
    <div>
        <div class='d-flex flex-row justify-content-between align-items-start'>
            <div class="mr-auto"><slot></slot></div>
            <div v-if="ranges=='date-interval'">
                <el-date-picker size='mini' 
                    v-model='filter.range' type='daterange'
                    format="dd/MM/yyyy"
                    value-format='yyyy-MM-dd'
                    end-placeholder='Data Fim'
                    start-placeholder='Data início'>
                </el-date-picker>
            </div>
            <template v-else>
                <div v-if="options.length>0"><v-select size="mini" v-model='filter.range' :optionlist="options" withoutBlank /></div>
            </template>
        </div>
        <div class="d-flex flex-column justify-content-between" ref="content" style="display:none;">
            <h2 v-loading="loading">{{value ? value.toFixed(2).replace(/[.,]00$/, "") : 0}}</h2>
            <div class="mt-3">
                <span v-html="trend"></span>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props : ["route","time","ranges"],
    data() {
        return {
            loaded : false,
            data : [],
            loading : false,
            filter :{},
            options : [],
            compare : 0,
            value : null
        }
    },
    async created() {
        let result = []
        let ranges = this.ranges
        if(ranges=="date-interval")  this.initDateInterval()
        else {
            let keys = Object.keys(ranges)
            for(let i in keys) result.push({name:keys[i],id:ranges[keys[i]]})
            this.options = result
            if(!this.filter.range && result[0]) this.$set(this.filter,"range",result[0].id)
        }
        this.updateData()
        setInterval(_ => {
            this.updateData()
        },this.time*1000)
    },
    computed : {
        trend() {
            let percent = this.getTrendPercent()
            if(Number(percent)==0) return "<div class='d-flex align-items-center'><h5>Sem Alteração</h5></div>"
            if(Number(this.value)<Number(this.compare)) return `<div class='d-flex align-items-center'><h5>${percent}%<span class='el-icon-bottom-left text-danger ml-2'></span></h5></div>`
            return `<div class='d-flex align-items-center'><h5>${percent}%<span class='el-icon-top-right text-success ml-2'></span></h5></div>`
        }
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
    methods : {
        initDateInterval() {
            let endDate = new Date()
            let startDate = new Date(new Date().setDate(endDate.getDate()-15))
            this.$set(this.filter,"range",[startDate.toISOString().slice(0, 10),endDate.toISOString().slice(0, 10)])
        },
        getTrendPercent() {
            let compare = Number(this.compare)
            let value = compare-Number(this.value)
            if(value<=0) return (value*-1).toFixed(2).replace(/[.,]00$/, "")
            let result = ((value*100)/compare)
            if(result<0) result*=-1
            return result.toFixed(2).replace(/[.,]00$/, "")
        },
        updateData() {
            if(!this.filter.range) return
            this.loading = true
            this.$http.post(this.route,this.filter).then( res => {
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