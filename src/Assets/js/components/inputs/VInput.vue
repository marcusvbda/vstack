<!-- eslint-disable vue/no-parsing-error -->
<template>
    <CustomResourceComponent :label="label" :description="description">
        <div class="flex flex-col">
            <slot name="prepend-slot" />
            <template v-if="type == 'percentage'">
                <div class="progress-section">
                    <ElProgress
                        type="dashboard"
                        :percentage="parseInt(!val ? '0' : val)"
                    />
                    <input
                        :disabled="disabled"
                        class="form-control"
                        v-model="val"
                        v-bind:class="{ 'is-invalid': errors }"
                        :placeholder="placeholder ? placeholder : ''"
                        :min="min"
                        @change="validMinMax"
                        :max="max"
                        type="number"
                        :step="step"
                        @blur="$emit('blur', val)"
                    />
                </div>
            </template>
            <template v-else>
                <el-input
                    v-if="!mask"
                    :placeholder="placeholder ? placeholder : ''"
                    :disabled="disabled"
                    @blur="$emit('blur', val)"
                    v-model="val"
                    :maxlength="maxlength"
                    :rows="rows"
                    class="w-full"
                    :type="type"
                >
                    <template slot="prepend" v-if="prepend">
                        <span v-html="prepend ? prepend : ''" />
                    </template>
                    <template slot="append" v-if="append">
                        <span v-html="append ? append : ''" />
                    </template>
                </el-input>
                <el-input
                    v-else
                    :placeholder="placeholder ? placeholder : ''"
                    :disabled="disabled"
                    @blur="$emit('blur', val)"
                    v-model="val"
                    :maxlength="maxlength"
                    :rows="rows"
                    class="w-full"
                    v-mask="mask"
                >
                    <template slot="prepend" v-if="prepend">
                        <span v-html="prepend ? prepend : ''" />
                    </template>
                    <template slot="append" v-if="append">
                        <span v-html="append ? append : ''" />
                    </template>
                </el-input>
            </template>
            <div class="mt-2 ml-2" v-if="errors">
                <ul class="text-sm text-red-700">
                    <li v-for="(e, i) in errors" :key="i" v-html="e" />
                </ul>
            </div>
        </div>
        <div class="w-full mt-2 flex justify-end" v-if="show_value_length">
            <small class="text-neutral-400" v-html="limitText" />
        </div>
        <slot name="append-slot" />
    </CustomResourceComponent>
</template>
<script>
import { mask } from 'vue-the-mask';
export default {
    props: [
        'rows',
        'label',
        'type',
        'placeholder',
        'errors',
        'prepend',
        'append',
        'disabled',
        'mask',
        'description',
        'maxlength',
        'min',
        'value',
        'step',
        'show_value_length',
    ],
    directives: { mask },
    data() {
        return {
            val: null,
        };
    },
    watch: {
        val(val) {
            return this.$emit('input', val);
        },
        value(val) {
            this.val = val;
        },
    },
    computed: {
        rest() {
            return this.max - (this.val || '').length;
        },
        limitText() {
            return `${this.rest}/${this.max}`;
        },
        max() {
            return parseInt(this.maxlength ? this.maxlength : 0);
        },
    },
    created() {
        if (this.type === 'slider') {
            this.val = parseInt(this.value ?? 0);
        } else {
            this.val = this.value;
        }
    },
    methods: {
        validMinMax() {
            if (['number', 'slider'].includes(this.type)) {
                if (this.min !== undefined) {
                    if (this.val < this.min) {
                        this.val = this.min;
                    }
                    if (this.val > this.max) {
                        this.val = this.max;
                    }
                }
            }
        },
    },
};
</script>
