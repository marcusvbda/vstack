<template>
    <div>
        <div class="form-group row mb-3">
            <label class="col-sm-2 col-form-label" v-if="label"><span v-html="label ? label : ''"></span></label>
            <div class="col-sm-10" v-bind:class="{'col-sm-10' : label,'col-sm-12':!label}">
                <div class="input-group">
                    <div class="input-group-prepend" v-if="prepend">
                        <span class="input-group-text">
                            <span v-html="prepend ? prepend : ''"></span>
                        </span>
                    </div>
                    <the-mask :disabled="disabled" class="form-control" v-if="mask" :mask="mask" v-model="val" v-bind:class="{'is-invalid' : errors}" :placeholder="placeholder ? placeholder : ''" name="email" :type="type ? type : 'text'" />
                    <input :disabled="disabled" class="form-control" v-else v-model="val" v-bind:class="{'is-invalid' : errors}" :placeholder="placeholder ? placeholder : ''" name="email" :type="type ? type : 'text'">
                    <div class="input-group-append" v-if="append">
                        <span class="input-group-text">
                            <span v-html="append ? append : ''"></span>
                        </span>
                    </div>
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
    props:["label","type","placeholder","errors","prepend","append","disabled","mask"],
    data() {
        return {
            val : null
        }
    },
    watch: {
        val(val) {
            return this.$emit("input",val)
        }
    },
    async created() {
        this.val = this.$attrs.value
    }
}
</script>