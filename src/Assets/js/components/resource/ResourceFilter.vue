<template>
    <div
        class="d-flex flex-row row align-items-center mb-3 flex-wrap mb-4"
        v-if="data.filters.length>0"
    >
        <template v-for="(filter,key) in data.filters">
            <div class="col-md-3 col-sm-12 mb-3" :key="key">
                <label
                    class="mb-0"
                    style="font-size:14px;"
                    v-if="filter.label"
                    v-html="filter.label"
                />
                <v-runtime-template :key="key" :template="filter.view" />
            </div>
        </template>
    </div>
</template>
<script>
import VRuntimeTemplate from "v-runtime-template"
export default {
    props: ["data"],
    data() {
        return {
            filter: {},
            timeout: null
        }
    },
    components: {
        "v-runtime-template": VRuntimeTemplate
    },
    async created() {
        this.initFormFilter()
    },
    methods: {
        setFormValue(index, value, filter) {
            if (filter.component == "text-filter") value = String(value)
            if (filter.component == "check-filter") value = value === "true"
            if (filter.component == "select-filter") value = value ? (!isNaN(Number(value)) ? Number(value) : "") : ""
            if (filter.component == "rangedate-filter") value = value.split(",")
            this.$set(this.filter, index, value)
        },
        initFormFilter() {
            let filter_keys = Object.keys(this.data.filters)
            for (let i in filter_keys) {
                if (this.data.filters[filter_keys[i]]) {
                    if (this.data.filters[filter_keys[i]].index)
                        this.setFormValue(this.data.filters[filter_keys[i]].index, this.data.query[this.data.filters[filter_keys[i]].index] ? this.data.query[this.data.filters[filter_keys[i]].index] : '', this.data.filters[filter_keys[i]])
                }
            }
        },
        makeNewRoute() {
            let str_query = ""
            let filter_keys = Object.keys(this.filter)
            filter_keys.forEach(key => this.data.query[key] = this.filter[key])
            Object.keys(this.data.query).forEach(key => {
                if ((key != "page") && (key != "_")) {
                    if (!["null", null].includes(this.data.query[key])) {
                        str_query += `${key}=${this.data.query[key]}&`
                    }
                }
            })
            if (this.data.query["_"]) str_query += `${str_query ? "&" : ""}_=${this.data.query["_"] ? this.data.query["_"] : ""}`
            str_query = str_query.slice(0, -1)
            window.location.href = `${this.data.route}?${str_query}`
        }
    },
}
</script>
