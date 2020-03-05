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
    props: ["rendered_data", "form"],
    methods: {
        getTableValue(data, index, value, x) {
            if (typeof value == "object") value = index
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
            for (let i in this.rendered_data.crud_fields) {
                let field = this.rendered_data.crud_fields[i].options.field
                this.$set(this.form, field, String(data[field]))
            }
            this.$set(this.form, 'id', data.id)
            this.$emit("onEdit")
        }
    }
}
</script>