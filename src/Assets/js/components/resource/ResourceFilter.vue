<template>
    <div class="d-flex flex-row row align-items-center mb-3 flex-wrap mb-4" v-if="data.filters.length>0">
        <template v-for="(filter,key) in data.filters" >
            <div class="col-md-3 col-sm-12 mb-3">
                <label class="mb-0" style="font-size:14px;"><span v-html="filter.label"></span></label>
                <v-runtime-template :key="key" :template="filter.view" />
            </div>
        </template>
    </div>
</template>
<script>
import VRuntimeTemplate from "v-runtime-template"
export default {
    props :["data"],
    data() {
        return {
            filter : {},
        }
    },
    components : {
        "v-runtime-template" : VRuntimeTemplate
    },
    async created() {
        this.initFormFilter()
    },
    methods: {
        setFormValue(index,value,filter) {
            if(filter.component == "text-filter")        value = String(value)
            if(filter.component == "check-filter")       value = value==="true"
            if(filter.component == "select-filter")      value = value ? (!isNaN(Number(value)) ? Number(value) : "") : ""
            if(filter.component == "rangedate-filter")   value = value.split(",")
            this.$set(this.filter,index,value)
        },
        initFormFilter() {
            let filter_keys = Object.keys(this.data.filters)
            for(let i in filter_keys) {
                this.setFormValue(this.data.filters[filter_keys[i]].index,this.data.query[this.data.filters[filter_keys[i]].index] ? this.data.query[this.data.filters[filter_keys[i]].index] : '',this.data.filters[filter_keys[i]])
            }
        },
        makeNewRoute() {
            let str_query = ""
            let filter_keys = Object.keys(this.filter)
            for(let i in filter_keys) this.data.query[filter_keys[i]] = this.filter[filter_keys[i]]
            for(let i in this.data.query) {
                if((i!="page")&&(i!="_")) {
                    if(!["null",null].includes(this.data.query[i])) {
                        str_query += `${i}=${this.data.query[i]}&`
                    }
                }
            }
            str_query = str_query.slice( 0, -1 )
            if(this.data.query["_"]) {
                str_query += `${str_query ? "&" : ""}_=${this.data.query["_"] ? this.data.query["_"] : ""}`
            }
            window.location.href =  `${this.data.route}?${str_query}`
        }
    },
}
</script>