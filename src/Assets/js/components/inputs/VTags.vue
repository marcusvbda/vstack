<template>
    <div class="form-group row mb-3">
            <label class="col-sm-2 col-form-label" v-if="label"><span v-html="label ? label : ''"></span></label>
            <div class="col-sm-10 d-flex flex-column" v-bind:class="{'col-sm-10' : label,'col-sm-12':!label}">
                <div class="d-flex flex-row align-items-center">
                    <el-tag
                        class="mr-2"
                        :key="tag"
                        v-for="tag in dynamicTags"
                        :closable="disabled!=true ? true : false"
                        :disable-transitions="false"
                        @keydown="$event.keyCode === 13 ? $event.preventDefault() : false"
                        @close="handleClose(tag)">
                        {{tag}}
                    </el-tag>
                    <template v-if="disabled!=true">
                        <el-input
                            style="width: 300px;"
                            class="input-new-tag ml-0"
                            v-if="inputVisible"
                            v-model="inputValue"
                            ref="saveTagInput"
                            size="medium"
                            @keyup.enter.native="handleInputConfirm"
                            @blur="handleInputConfirm"
                        >
                        </el-input>
                        <button type="button"  style="width: 300px;" v-else class=" ml-0 btn btn-primary btn-sm button-new-tag" size="small" @click="showInput">+ Adicionar</button>
                    </template>
                </div>
                <small style="color: gray;" v-if="description"><span v-html="description"></span></small>
            </div>
        </div>
    </div>
</template>
<script>
  export default {
    props : ["placeholder","label","route_list","list_model","disabled","errors","optionlist","required","size","multiple","relation","allowcreate","unique","description","extraValidator"],
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
            if(!this.inputVisible) return
            if(this.unique) {
                if(this.dynamicTags.find(x=>x == this.inputValue)) return this.$message({ showClose: true, message: `${this.inputValue} já existe`, type: "error" })
            }
            let inputValue = this.inputValue;
            if (inputValue) {
                if(this.extraValidator) {
                    let valid = this.extraValidator.handle( inputValue)
                    if(!valid) return this.$message({ showClose: true, message: this.extraValidator.message, type: "error" })
                }
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