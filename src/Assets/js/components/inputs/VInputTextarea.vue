<template>
    <CustomResourceComponent :label="label" :description="description">
        <div class="d-flex flex-column">
            <div class="input-group">
                <div class="input-group-prepend" v-if="prepend">
                    <span class="input-group-text">
                        <span v-html="prepend ? prepend : ''"></span>
                    </span>
                </div>
                <textarea :disabled="disabled" class="form-control" v-model="val" style="resize: none"
                    v-bind:class="{ 'is-invalid': errors }" :rows="rows" :placeholder="placeholder ? placeholder : ''"
                    name="email" :type="type ? type : 'text'" :maxlength="maxlength"></textarea>
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
            <small class="text-muted text-right" v-html="limitText" v-if="show_value_length" />
        </div>
    </CustomResourceComponent>
</template>
<script>
export default {
    props: ["label", "type", "placeholder", "errors", "prepend", "append", "disabled", "rows", "description", "maxlength", "show_value_length"],
    data() {
        return {
            val: null,
        };
    },
    computed: {
        rest() {
            return this.max - (this.val || "").length;
        },
        limitText() {
            return `${this.rest}/${this.max}`;
        },
        max() {
            return parseInt(this.maxlength ? this.maxlength : 0);
        },
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
