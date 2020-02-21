<template>
    <div class="form-group row mb-3">
            <label class="col-sm-2 col-form-label" v-if="label"><span v-html="label ? label : ''"></span></label>
            <div class="col-sm-10" v-bind:class="{'col-sm-10' : label,'col-sm-12':!label}">
                <div class="input-group v-select"  v-bind:class="{'is-invalid' : errors}">
                    <el-select :allow-create="allowcreate" :disabled="disabled" :multiple="multiple" :size="(size ? size : 'large')" class="w-100" clearable v-model="value" filterable :placeholder="placeholder" v-loading="loading"
                    >
                        <el-option v-if="required==undefined" label="" value=""></el-option>
                        <el-option v-for="(item,i) in options" :key="i" :label="item.name" :value="Number.isInteger(item.id) ? item.id : String(item.id)"></el-option>
                    </el-select>
                    <div class="invalid-feedback" v-if="errors">
                        <ul class="pl-3 mb-0">
                            <li v-for="(e,i) in errors" :key="i">{{e}}</li>
                        </ul>
                    </div>
                    <small v-if="description" class="mt-1" style="color: gray;"><span v-html="description"></span></small>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props : ["placeholder","label","route_list","list_model","disabled","errors","optionlist","required","size","multiple","relation","allowcreate","option_list","description"],
    data() {
        return {
            loading : true,
            started : false,
            options: [],
            value: this.multiple ? [] : null
      }
    },
    async created() {
        if(this.list_model)
        {
            this.initOptions( _ => {
                this.value = this.$attrs.value ? this.$attrs.value : (this.multiple ? [] : null)
                this.loading = false
                this.started = true
            })
        } else {
            for(let i in this.option_list) this.options.push({id:this.option_list[i],value:this.option_list[i]})
            this.value = this.$attrs.value ? this.$attrs.value : (this.multiple ? [] : null)
            this.loading = false
            this.started = true
        }
    },
    watch : {
        value(val){
            if(this.started) {
                return this.$emit("input",val)
            }
        }
    },
    methods : {
        initOptions(callback){
            if(this.optionlist||(!this.list_model)) {
                this.options = this.optionlist ? this.optionlist : []
                return callback()
            }
            this.$http.post(this.route_list,{model:this.list_model}).then( res => {
                res = res.data
                this.options = res.data
                callback()
            }).catch(er => {
                this.loading - false
                console.log(er)
            })
        }
    }
}
</script>
<style scoped lang="scss">
.v-select {
    &.is-invalid {
        .el-select {
            border: 1px solid red;
        }
        .invalid-feedback {
            display:block;
        }
    }
}
</style>