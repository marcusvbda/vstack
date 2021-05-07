<template>
    <div>
        <portal :to="`resource_after_row_arrow_${row_id}`">
            <span class="el-icon-arrow-down arrow-after-icon" v-if="!visible" @click="visible = true" />
            <span class="el-icon-arrow-up arrow-after-icon" v-else @click="visible = false" />
        </portal>
        <transition name="fade">
            <div class="container-fluid content py-3" v-if="visible"><slot /></div>
        </transition>
        <div class="spacer"></div>
    </div>
</template>
<script>
export default {
    props: ['row_id'],
    data() {
        return {
            visible: false,
        }
    },
}
</script>