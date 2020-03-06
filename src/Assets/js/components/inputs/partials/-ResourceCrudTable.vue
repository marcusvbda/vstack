<template>
    <div>
        <div class="table-responsive-sm" v-if="rendered_data.data_count>0">
            <table class="table table-striped hovered resource-table table-hover mb-0">
                <thead>
                    <tr>
                        <th
                            v-for="(value,i) in rendered_data.table"
                            :key="i"
                        >{{ value["label"] ? value["label"] : value}}</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(data,d) in rendered_data.data" :key="d">
                        <template v-for="(value,i,x) in rendered_data.table">
                            <td
                                :key="i"
                                v-if="(!(x == 0 || value =='name'))"
                                v-html="getTableValue(data,i,value,x)"
                            ></td>
                            <td :key="i" v-else>
                                <a
                                    href="#"
                                    class="link"
                                    @click.prevent="edit(data)"
                                    v-html="getTableValue(data,i,value,x)"
                                ></a>
                            </td>
                        </template>
                    </tr>
                </tbody>
            </table>
        </div>
        <template v-else>
            <h4 class="text-center my-4" v-html="rendered_data.no_results_found_text"></h4>
        </template>
    </div>
</template>
<script>
export default {
    props: ["rendered_data", "form", "resourceData"],
    methods: {
        getTableValue(data, index, value, x) {
            if (typeof value == "object") value = index
            index = String(index)
            if (index.indexOf("->") < 0) return data[value]
            let indexes = index.split("->")
            let _val = data
            for (let i in indexes) {
                _val = _val[indexes[i]] ? _val[indexes[i]] : '-'
            }
            let text = _val ? _val : data[index]

            return text
        },
        edit(data) {
            let fields = this.rendered_data.crud_fields
            for (let i in fields) {
                let field_name = this.rendered_data.crud_fields[i].options.field
                let field_value = data[field_name]
                field_value = !["null", ""].includes(field_value) ? (Array.isArray(field_value) ? field_value : String(field_value)) : fields[i].options.default
                if (fields[i].options.type == "check") field_value = (String(field_value) == "true")
                if (!["password", "password_confirmation"].includes(field_name)) {
                    if (fields[i].options.type == "upload") {
                        if (field_value == "null") field_value = null
                        else if (typeof field_value == "string") field_value = Array(field_value)

                        this.$set(fields[i].options.type == "resource-field" ? this.resourceData : this.form, field_name, field_value)
                    } else {
                        if (fields[i].options.type == "resource-field") {
                            let _params = Object.assign({}, fields[i].options.params)
                            for (let i in _params) {
                                _params[i] = data[_params[i]]
                            }
                            this.$set(this.resourceData, field_name, _params)
                        } else {
                            this.$set(this.form, field_name, field_value)
                        }
                    }
                }
            }
            this.$set(this.form, 'id', data.id)
            this.$emit("onEdit")
        }
    }
}
</script>