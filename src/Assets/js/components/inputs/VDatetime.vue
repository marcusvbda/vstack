<template>
    <CustomResourceComponent :label="label" :description="description">
        <div class="d-flex flex-column">
            <div class="input-group">
                <div class="input-group-prepend" v-if="prepend">
                    <span class="input-group-text">
                        <span v-html="prepend ? prepend : ''"></span>
                    </span>
                </div>
                <el-date-picker v-model="val" :type="type" class="w-100" v-bind:class="{ 'is-invalid': errors }"
                    range-separator=" - " :start-placeholder="start_placeholder" :end-placeholder="end_placeholder"
                    :placeholder="placeholder" :format="format" :value-format="value_format">
                </el-date-picker>
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
    </CustomResourceComponent>
</template>
<script>
export default {
    props: [
        "label",
        "type",
        "format",
        "value_format",
        "start_placeholder",
        "end_placeholder",
        "placeholder",
        "errors",
        "prepend",
        "append",
        "disabled",
        "mask",
        "description",
    ],
    data() {
        return {
            val: null,
        };
    },
    watch: {
        val(val) {
            return this.$emit("input", val);
        },
    },
    created() {
        this.val = this.$attrs.value;
    },
};
</script>
