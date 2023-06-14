<template>
    <CustomResourceComponent :label="label" :description="description">
        <div class="flex flex-col">
            <slot name="prepend-slot" />
            <el-date-picker
                v-model="val"
                :type="type"
                range-separator=" - "
                :disabled="disabled"
                :start-placeholder="start_placeholder"
                :end-placeholder="end_placeholder"
                :placeholder="placeholder"
                :format="format"
                :value-format="value_format"
            >
                <template slot="prepend" v-if="prepend">
                    <span v-html="prepend ? prepend : ''" />
                </template>
                <template slot="append" v-if="append">
                    <span v-html="append ? append : ''" />
                </template>
            </el-date-picker>
            <div class="mt-2 ml-2" v-if="errors">
                <ul class="text-sm text-red-700">
                    <li v-for="(e, i) in errors" :key="i" v-html="e" />
                </ul>
            </div>
        </div>
    </CustomResourceComponent>
</template>
<script>
export default {
    props: [
        'label',
        'type',
        'format',
        'value_format',
        'start_placeholder',
        'end_placeholder',
        'placeholder',
        'errors',
        'prepend',
        'append',
        'disabled',
        'mask',
        'description',
    ],
    data() {
        return {
            val: null,
        };
    },
    watch: {
        val(val) {
            return this.$emit('input', val);
        },
    },
    created() {
        this.val = this.$attrs.value;
    },
};
</script>
