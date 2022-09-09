<template>
    <tr>
        <td class="w-25">
            <div class="d-flex flex-column">
                <b class="input-title" v-if="label" v-html="label" />
                <small class="text-muted mt-1" v-if="description" v-html="description" />
            </div>
        </td>
        <td :class="custom_class">
            <slot />
        </td>
    </tr>
</template>
<script>
export default {
    props: ["label", "description", "custom_class"]
};
</script>
