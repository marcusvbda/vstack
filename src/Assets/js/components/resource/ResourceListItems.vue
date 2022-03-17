<template>
    <div class="row list-items">
        <div v-for="(list_item, i) in list_items" :key="i" :class="`col list-item ${list_item.class ? list_item.class : ''}`">
            <v-runtime-template v-if="list_item.template" :key="i" :template="list_item.template" />
            <div class="list-item--content" v-else>
                <b class="list-item--title" v-html="list_item.label" />
                <div class="list-item--value text-muted" v-html="list_item.value" />
            </div>
        </div>
    </div>
</template>
<script>
import VRuntimeTemplate from "v-runtime-template";

export default {
    props: ["list_items"],
    components: {
        "v-runtime-template": VRuntimeTemplate,
    },
};
</script>
