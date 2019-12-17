<template>
    <div class="form-group row mb-3">
            <label class="col-sm-2 col-form-label" v-if="label"><span v-html="label ? label : ''"></span></label>
            <div class="col-sm-10 d-flex align-items-center" v-bind:class="{'col-sm-10' : label,'col-sm-12':!label}">
                <el-tag
                    class="mr-2"
                    :key="tag"
                    v-for="tag in dynamicTags"
                    closable
                    :disable-transitions="false"
                    @keydown="$event.keyCode === 13 ? $event.preventDefault() : false"
                    @close="handleClose(tag)">
                    {{tag}}
                </el-tag>
                <el-input
                    class="input-new-tag ml-0"
                    v-if="inputVisible"
                    v-model="inputValue"
                    ref="saveTagInput"
                    size="medium"
                    @keyup.enter.native="handleInputConfirm"
                    @blur="handleInputConfirm"
                >
                </el-input>
                <button type="button" v-else class=" ml-0 btn btn-primary btn-sm button-new-tag" size="small" @click="showInput">+ Adicionar</button>
            </div>
        </div>
    </div>
</template>
<script>
  export default {
    props : ["placeholder","label","route_list","list_model","disabled","errors","optionlist","required","size","multiple","relation","allowcreate","unique"],
    data() {
        return {
            dynamicTags: [],
            inputVisible: false,
            started : false,
            inputValue: ''
        };
    },
    watch : {
        dynamicTags() {
            if(this.started) {
                return this.$emit("input",val)
            }
        }
    },
    mounted() {
        this.dynamicTags = this.$attrs.value ? this.$attrs.value : []
    },
    methods: {
        handleClose(tag) {
            this.dynamicTags.splice(this.dynamicTags.indexOf(tag), 1);
        },

        showInput() {
            this.inputVisible = true;
            this.$nextTick(_ => {
                this.$refs.saveTagInput.$refs.input.focus();
            });
        },

        handleInputConfirm() {
            if(this.unique) {
                if(this.dynamicTags.find(x=>x == this.inputValue)) return this.$message({ showClose: true, message: `${this.inputValue} já existe`, type: "error" })
            }
            let inputValue = this.inputValue;
            if (inputValue) {
                this.dynamicTags.push(inputValue);
            }
            this.inputVisible = false;
            this.inputValue = '';
        }
    }
  }
</script>
<style>
  .el-tag + .el-tag {
        margin-left: 10px;
  }
  .button-new-tag {
        margin-left: 10px;
        height: 32px;
        line-height: 30px;
        padding-top: 0;
        padding-bottom: 0;
  }
  .input-new-tag {
        width: 90px;
        margin-left: 10px;
        vertical-align: bottom;
  }
</style>