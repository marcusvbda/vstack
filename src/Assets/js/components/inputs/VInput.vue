<template>
    <tr>
        <td class="w-25">
            <div class="d-flex flex-column">
                <span class="input-title" v-if="label" v-html="label ? label : ''" />
                <small v-if="description" class="mt-1 text-muted">
                    <span v-html="description"></span>
                </small>
            </div>
        </td>
        <td>
            <div class="d-flex flex-column">
                <div class="input-group">
                    <div class="input-group-prepend" v-if="prepend">
                        <span class="input-group-text">
                            <span v-html="prepend ? prepend : ''"></span>
                        </span>
                    </div>
                    <template v-if="type == 'password'">
                        <input
                            :disabled="disabled"
                            class="form-control"
                            v-model="val"
                            v-bind:class="{ 'is-invalid': errors }"
                            :placeholder="placeholder ? placeholder : ''"
                            :maxlength="maxlength"
                            :type="protected ? 'password' : 'text'"
                            @blur="$emit('blur', val)"
                        />
                        <el-button size="small" @click.prevent="protected = !protected">
                            <i class="el-icon-lock" v-if="protected" />
                            <i class="el-icon-unlock" v-else />
                        </el-button>
                    </template>
                    <template v-else-if="type == 'slider'">
                        <el-slider v-model="val" show-input :step="step" :max="maxlength" />
                    </template>
                    <template v-else>
                        <currency-input
                            v-if="type == 'currency'"
                            class="form-control"
                            v-bind:class="{ 'is-invalid': errors }"
                            currency="BRL"
                            :placeholder="placeholder ? placeholder : ''"
                            :auto-decimal-mode="true"
                            v-model="val"
                            :maxlength="maxlength"
                            @blur="$emit('blur', val)"
                        />
                        <template v-else>
                            <the-mask
                                :disabled="disabled"
                                class="form-control"
                                v-if="mask"
                                :mask="mask"
                                :masked="true"
                                v-model="val"
                                v-bind:class="{ 'is-invalid': errors }"
                                :placeholder="placeholder ? placeholder : ''"
                                :maxlength="maxlength"
                                @blur="$emit('blur', val)"
                                :type="type ? type : 'text'"
                            />
                            <input
                                :disabled="disabled"
                                class="form-control"
                                v-else
                                v-model="val"
                                v-bind:class="{ 'is-invalid': errors }"
                                :placeholder="placeholder ? placeholder : ''"
                                :maxlength="maxlength"
                                :type="type ? type : 'text'"
                                :step="step"
                                @blur="$emit('blur', val)"
                            />
                        </template>
                    </template>
                    <div class="input-group-append" v-if="append">
                        <span class="input-group-text">
                            <span v-html="append ? append : ''"></span>
                        </span>
                    </div>
                    <div class="invalid-feedback" v-if="errors">
                        <ul class="pl-3 mb-0">
                            <li v-for="(e, i) in errors" :key="i" v-html="e" />
                        </ul>
                    </div>
                </div>
            </div>
        </td>
    </tr>
</template>
<script>
export default {
    props: [
        "label",
        "type",
        "placeholder",
        "errors",
        "prepend",
        "append",
        "disabled",
        "mask",
        "description",
        "maxlength",
        "value",
        "step",
    ],
    data() {
        return {
            val: null,
            protected: true,
        };
    },
    watch: {
        val(val) {
            return this.$emit("input", val);
        },
        value(val) {
            this.val = val;
        },
    },
    async created() {
        this.val = this.value;
    },
};
</script>
<style>
.el-slider.el-slider--with-input {
    width: 100%;
}

.el-input-number__decrease,
.el-input-number__increase {
    height: 96%;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
