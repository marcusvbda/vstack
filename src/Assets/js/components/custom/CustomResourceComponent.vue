<template>
    <tr class="flex flex-col md:table-row">
        <td class="field-title" v-if="show_label">
            <div class="flex flex-col">
                <b
                    class="input-title text-neutral-700 text-sm"
                    v-if="label"
                    v-html="label"
                />
                <small
                    class="text-neutral-400 mt-1 input-title text-xs break-words"
                    v-if="description"
                    v-html="description"
                />
            </div>
        </td>
        <td :class="`${custom_class} field-slot`" :colspan="show_label ? 1 : 2">
            <slot />
        </td>
    </tr>
</template>
<script>
export default {
    props: {
        label: {
            type: String,
            default: '',
        },
        description: {
            type: String,
            default: '',
        },
        custom_class: {
            type: String,
            default: '',
        },
        show_label: {
            type: Boolean,
            default: true,
        },
    },
};
</script>
