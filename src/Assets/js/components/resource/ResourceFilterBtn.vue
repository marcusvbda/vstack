<template>
    <div class="dropdown" style="position:relative;" v-if="data.filters.length>0">
        <span class="badge-number" v-if="qty_filters>0" v-html="qty_filters" />
        <button
            :class="`btn btn dropdown-toggle ${qty_filters<=0 ? 'clean-filter' : 'with-filter'} btn-sm`"
            type="button"
            id="dropdownMenuButton"
            data-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
        >Filtros Avançados</button>
        <div class="dropdown-menu filter-content dropdown-menu-right">
            <div class="row">
                <div class="col-12">
                    <table class="table table-striped table-sm">
                        <tbody>
                            <template v-for="(filter,key) in data.filters">
                                <tr :key="`${key}_label`" v-if="filter.label">
                                    <td class="px-2">
                                        <label
                                            class="mb-0 text-muted"
                                            style="font-size:13px;font-weight:bold;"
                                            v-if="filter.label"
                                            v-html="filter.label"
                                        />
                                    </td>
                                </tr>
                                <tr :key="`${key}_input`">
                                    <td class="px-2">
                                        <v-runtime-template :key="key" :template="filter.view" />
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
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
    computed: {
        qty_filters() {
            const qty = Object.keys(this.filter).map(key => {
                if (this.filter[key]) {
                    if (Array.isArray(this.filter[key])) { return this.filter[key].length > 1 ? true : null }
                    return true
                }
            }).filter(x => x).length
            return qty
        }
    },
    created() {
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
<style lang="scss">
.badge-number {
    background-color: #04a9ce;
    color: white;
    font-size: 10;
    padding: 2px;
    border-radius: 100%;
    position: absolute;
    z-index: 99;
    top: -5px;
    left: -5px;
    width: 15px;
    height: 15px !important;
    display: flex;
    align-items: center;
    justify-content: center;
}
.filter-content {
    width: 700;
    padding: 0;
    border-radius: 10px;
    border-color: #bacad6;
}
.with-filter {
    background-color: #ff7512;
    &:hover {
        background-color: #a74906;
        color: white;
        transition: 0.4s;
    }
}
.clean-filter {
    border-color: #343a40;
    background-color: transparent;
    color: #343a40;
    &:hover {
        background-color: #343a40;
        color: white;
        transition: 0.4s;
    }
}
</style>