<template>
    <div ref="content" style="display:none;">
        <div class='d-flex flex-row justify-content-between align-items-start pt-3 px-3 pb-1'>
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
                <div v-if="options.length>0"><v-select size="mini" v-model='filter.range' :optionlist="options" required /></div>
            </template>
        </div>
        <div class="d-flex flex-column justify-content-between p-1" v-loading="loading" >
            <area-chart :discrete="true" :data="value" height="120px" ></area-chart>
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
            filter : {},
            options : [],
            value : {}
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
        updateData() {
            if(!this.filter.range) return
            this.loading = true
            this.$http.post(this.route,this.filter).then( res => {
                let data = res.data ? res.data : {}
                this.value = data
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