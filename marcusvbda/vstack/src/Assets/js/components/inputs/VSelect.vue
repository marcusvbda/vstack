<template>
    <div class="form-group row mb-3">
            <label class="col-sm-2 col-form-label" v-if="label"><span v-html="label ? label : ''"></span></label>
            <div class="col-sm-10" v-bind:class="{'col-sm-10' : label,'col-sm-12':!label}">
                <div class="input-group v-select"  v-bind:class="{'is-invalid' : errors}">
                    <el-select :disabled="disabled" :size="(size ? size : 'large')" class="w-100" clearable v-model="value" filterable :placeholder="placeholder" v-loading="loading"
                    
                    >
                        <el-option v-if="withoutBlank==undefined" label="" value=""></el-option>
                        <el-option v-for="(item,i) in options" :key="i" :label="item.name" :value="item.id"></el-option>
                    </el-select>
                    <div class="invalid-feedback" v-if="errors">
                        <ul class="pl-3 mb-0">
                            <li v-for="(e,i) in errors">{{e}}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props : ["placeholder","label","route_list","list_model","disabled","errors","optionlist","withoutBlank","size"],
    data() {
        return {
            loading : true,
            options: [],
            value: null
      }
    },
    async created() {
        this.initOptions( _ => {
            this.value = this.$attrs.value ? this.$attrs.value : null
            this.loading = false
        })
    },
    watch : {
        value(val){
            return this.$emit("input",val)
        }
    },
    methods : {
        initOptions(callback){
            if(this.optionlist) {
                this.options = this.optionlist
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