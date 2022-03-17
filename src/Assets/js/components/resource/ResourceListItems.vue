<template>
    <div class="row list-items">
        <div v-for="(list_item, i) in list_items" :key="i" :class="`col list-item ${list_item.class ? list_item.class : ''}`">
            <div class="list-item--content">
                <b class="list-item--title" v-html="list_item.label ? list_item.label : ''" />
                <div class="list-item--value text-muted" v-html="list_item.value ? list_item.value : ''" />
            </div>
        </div>
    </div>
</template>
<script>
export default {
    props: ["list_items"],
};
</script>
