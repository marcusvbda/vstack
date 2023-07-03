<template>
    <CustomResourceComponent :label="label" :description="description">
        <div class="flex flex-col">
            <slot name="prepend-slot" />
            <div class="flex flex-wrap items-center gap-2">
                <draggable v-model="dynamicTags" class="input-tag-row">
                    <transition-group>
                        <el-tag
                            :key="tag"
                            v-for="tag in dynamicTags"
                            :closable="disabled != true ? true : false"
                            :disable-transitions="false"
                            class="cursor-pointer input-tag"
                            @keydown="
                                $event.keyCode === 13
                                    ? $event.preventDefault()
                                    : false
                            "
                            @close="handleClose(tag)"
                        >
                            {{ tag }}
                        </el-tag>
                    </transition-group>
                </draggable>
                <template v-if="disabled != true">
                    <el-input
                        style="width: 160px"
                        v-if="inputVisible"
                        v-model="inputValue"
                        ref="saveTagInput"
                        size="medium"
                        @keyup.enter.native="handleInputConfirm"
                        @blur="handleInputConfirm"
                    >
                    </el-input>
                    <button
                        type="button"
                        v-else
                        class="ml-0 btn vstack-btn primary"
                        size="small"
                        @click="showInput"
                    >
                        + Adicionar
                    </button>
                </template>
            </div>
            <div
                class="invalid-feedback"
                v-if="errors"
                :style="{ display: `${errors ? 'block' : 'none'}` }"
            >
                <ul class="text-sm pl-3 text-red-700">
                    <li v-for="(e, i) in errors" :key="i" v-html="e" />
                </ul>
            </div>
            <slot name="append-slot" />
        </div>
    </CustomResourceComponent>
</template>
<script>
import draggable from 'vuedraggable';
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
        'unique',
        'description',
        'extraValidator',
    ],
    components: {
        draggable,
    },
    data() {
        return {
            dynamicTags: [],
            inputVisible: false,
            inputValue: '',
        };
    },
    watch: {
        dynamicTags(val) {
            return this.$emit('input', val);
        },
    },
    created() {
        this.dynamicTags = this.$attrs.value ? this.$attrs.value : [];
    },
    methods: {
        handleClose(tag) {
            this.dynamicTags.splice(this.dynamicTags.indexOf(tag), 1);
        },
        showInput() {
            this.inputVisible = true;
            this.$nextTick(() => {
                this.$refs.saveTagInput.$refs.input.focus();
            });
        },
        handleInputConfirm() {
            if (!this.inputVisible) return;
            if (this.unique) {
                if (this.dynamicTags.find((x) => x == this.inputValue))
                    return this.$message({
                        showClose: true,
                        message: `${this.inputValue} já existe`,
                        type: 'error',
                    });
            }
            let inputValue = this.inputValue;
            if (inputValue) {
                if (this.extraValidator) {
                    let valid = this.extraValidator.handle(inputValue);
                    if (!valid)
                        return this.$message({
                            showClose: true,
                            message: this.extraValidator.message,
                            type: 'error',
                        });
                }
                this.dynamicTags.push(inputValue);
            }
            this.inputVisible = false;
            this.inputValue = '';
        },
    },
};
</script>
<style lang="scss">
.input-tag-row span {
    display: flex;
    gap: 0.5rem;
    align-items: center;
    flex-wrap: wrap;
}
</style>
