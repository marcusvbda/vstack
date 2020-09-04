<template>
    <tbody>
        <tr v-if="f.label">
            <td class="px-2" style="position:relative;">
                <span class="badge-number" v-if="$parent.hasContent(index)" v-html="'!'" />
                <div class="d-flex flex-row justify-content-between">
                    <label
                        class="mb-0 text-muted"
                        style="font-size:13px;font-weight:bold;"
                        v-html="f.label ? f.label : k"
                    />
                    <el-switch v-model="visible" />
                </div>
            </td>
        </tr>
        <tr v-if="visible">
            <td class="px-2">
                <v-runtime-template :key="k" :template="f.view" />
            </td>
        </tr>
    </tbody>
</template>
<script>
import VRuntimeTemplate from "v-runtime-template"
export default {
    props: ["filter", "k", "data", "f", "index"],
    data() {
        return {
            visible: this.$parent.hasContent(this.index) ? true : false,
            timer: null
        }
    },
    components: {
        "v-runtime-template": VRuntimeTemplate,
    },
    methods: {
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
    }
}
</script>