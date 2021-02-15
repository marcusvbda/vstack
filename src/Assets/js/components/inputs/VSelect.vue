<template>
    <tr>
        <td v-if="label" v-html="label ? label : ''"></td>
            <td>
                <div class="d-flex flex-column">
                    <div class="input-group v-select"  v-bind:class="{'is-invalid' : errors}">
                        <el-select :allow-create="allowcreate" :disabled="disabled" :multiple="multiple" :size="(size ? size : 'large')" class="w-100" clearable v-model="value" filterable :placeholder="placeholder" v-loading="loading"
                        >
                            <el-option v-if="required==undefined" label="" value=""></el-option>
                            <el-option v-for="(item,i) in options" :key="i" :label="item.name" :value="String(item.id)"></el-option>
                        </el-select>
                        <div class="invalid-feedback" v-if="errors">
                            <ul class="pl-3 mb-0">
                                <li v-for="(e,i) in errors" :key="i">{{e}}</li>
                            </ul>
                        </div>
                        <small v-if="description" class="mt-1" style="color: gray;"><span v-html="description"></span></small>
                    </div>
                </div>
            </td>
        </td>
    </tr>
</template>
<script>
export default {
    props: [
        'placeholder',
        'label',
        'route_list',
        'list_model',
        'disabled',
        'errors',
        'optionlist',
        'required',
        'size',
        'multiple',
        'relation',
        'allowcreate',
        'option_list',
        'description',
    ],
    data() {
        return {
            loading: true,
            started: false,
            options: [],
            value: this.multiple ? [] : null,
        }
    },
    created() {
        this.initialize()
    },
    watch: {
        value(val) {
            if (this.started) {
                return this.$emit('input', val)
            }
        },
    },
    methods: {
        initialize() {
            if (this.optionlist) {
                this.initOptions((_) => {
                    this.value = this.$attrs.value ? this.$attrs.value : this.multiple ? [] : null
                    this.loading = false
                    this.started = true
                })
            } else {
                for (let i in this.option_list) {
                    let value = this.option_list[i]
                    if (typeof value == 'string') this.options.push({ id: value, value: value })
                }
                this.value = this.$attrs.value ? this.$attrs.value : this.multiple ? [] : null
                this.loading = false
                this.started = true
            }
        },
        initOptions(callback) {
            if (this.optionlist || !this.list_model) {
                this.options = this.optionlist ? this.optionlist : []
                return callback()
            }
            this.$http
                .post(this.route_list, { model: this.list_model })
                .then((res) => {
                    res = res.data
                    this.options = res.data
                    callback()
                })
                .catch((er) => {
                    this.loading = false
                    this.initialize()
                    console.log(er)
                })
        },
    },
}
</script>
<style scoped lang="scss">
.v-select {
    &.is-invalid {
        .el-select {
            border: 1px solid red;
        }
        .invalid-feedback {
            display: block;
        }
    }
}
</style>
