<template>
    <tr>
        <td class="w-25">
            <div class="d-flex flex-column">
                <b class="input-title" v-if="label" v-html="label ? label : ''" />
                <small v-if="description" class="mt-1 text-muted" v-html="description" />
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
                    <textarea
                        :disabled="disabled"
                        class="form-control"
                        v-model="val"
                        style="resize: none"
                        v-bind:class="{ 'is-invalid': errors }"
                        :rows="rows"
                        :placeholder="placeholder ? placeholder : ''"
                        name="email"
                        :type="type ? type : 'text'"
                    ></textarea>
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
    props: ['label', 'type', 'placeholder', 'errors', 'prepend', 'append', 'disabled', 'rows', 'description'],
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
