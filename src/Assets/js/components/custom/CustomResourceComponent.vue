<template>
    <tr>
        <td class="w-25 field-title" v-if="show_label">
            <div class="d-flex flex-column">
                <b class="input-title" v-if="label" v-html="label" />
                <small class="text-muted mt-1" v-if="description" v-html="description" />
            </div>
        </td>
        <td :class="custom_class" :colspan="show_label ? 1 : 2">
            <slot />
        </td>
    </tr>
</template>
<script>
export default {
    props: {
        label: {
            type: String,
            default: ""
        },
        description: {
            type: String,
            default: ""
        },
        custom_class: {
            type: String,
            default: ""
        },
        show_label: {
            type: Boolean,
            default: true
        }
    }
};
</script>
