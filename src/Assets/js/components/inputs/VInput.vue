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
                    <currency-input
                        v-if="type == 'currency'"
                        class="form-control"
                        v-bind:class="{ 'is-invalid': errors }"
                        currency="BRL"
                        :placeholder="placeholder ? placeholder : ''"
                        :auto-decimal-mode="true"
                        v-model="val"
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
                            :type="type ? type : 'text'"
                        />
                        <input
                            :disabled="disabled"
                            class="form-control"
                            v-else
                            v-model="val"
                            v-bind:class="{ 'is-invalid': errors }"
                            :placeholder="placeholder ? placeholder : ''"
                            :type="type ? type : 'text'"
                        />
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
    props: ['label', 'type', 'placeholder', 'errors', 'prepend', 'append', 'disabled', 'mask', 'description'],
    data() {
        return {
            val: null,
        }
    },
    watch: {
        val(val) {
            return this.$emit('input', val)
        },
    },
    async created() {
        this.val = this.$attrs.value
    },
}
</script>
