<template>
    <div>
        <div class="form-group row mb-3">
            <label class="col-sm-2 col-form-label" v-if="label"><span v-html="label ? label : ''"></span></label>
            <div class="col-sm-10" v-bind:class="{'col-sm-10' : label,'col-sm-12':!label}">
                <codemirror v-model="text" :options="cmOptions" />
            </div>
        </div>
    </div>
</template>
<script>
import { codemirror } from 'vue-codemirror'
import 'codemirror/mode/xml/xml.js'
import 'codemirror/theme/ambiance.css'
import'codemirror/addon/selection/active-line.js'
import'codemirror/addon/edit/closetag.js'
export default {
    props : ["mode","height","label"],
    data() {
        return {
            text : "",
            cmOptions: {
                tabSize: 4,
                styleActiveLine: true,
                lineNumbers: true,
                autoCloseTags: true,
                line: true,
                mode: this.mode ?  this.mode : 'text/html'
            }
        }
    },
    mounted() {
        this.text = this.$attrs.value
        $(".Codemirror").height(this.height)
    },
    watch : {
        text(val) {
            return this.$emit('input',val)
        }
    },
    components: {
        codemirror
    },
}
</script>

<style lang="scss">
@import "~codemirror/lib/codemirror.css";
    .CodeMirror {
        &.cm-s-default {
            border: 1px solid #e4e4e4;
        }
    }
</style>