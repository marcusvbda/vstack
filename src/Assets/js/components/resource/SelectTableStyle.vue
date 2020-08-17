<template>
    <div style="display:none;" ref="container" class="card select-table-style">
        <div class="card-header p-1">
            <div class="row d-flex align-items-center flex-wrap">
                <div class="col">
                    <slot name="lenses" />
                </div>
                <div
                    class="col d-flex flex-row justify-content-end"
                    v-if="list_type.includes('cards') && list_type.includes('table')"
                >
                    <div>
                        <span
                            @click="type='cards'"
                            :class="`${type=='cards' ? 'selected' : ''} el-icon-s-grid icon-select`"
                        />
                        <span
                            @click="type='table'"
                            :class="`${type=='table' ? 'selected' : ''} el-icon-s-unfold mr-1 icon-select`"
                        />
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <slot name="table" v-if="type=='table'" />
            <slot name="cards" v-if="type=='cards'" />
        </div>
    </div>
</template>
<script>
export default {
    props: ["id", "list_type"],
    data() {
        return {
            type: this.list_type[0],
            initalized: false
        }
    },
    created() {
        if (this.list_type.length <= 1) return
        const style = Cookies.get(`resource-table-style-${this.id}`)
        this.type = style ? style : this.list_type[0]
    },
    mounted() {
        this.$refs.container.style.display = 'block'
        this.initialized = true
    },
    watch: {
        type(val) {
            if (this.initialized) Cookies.set(`resource-table-style-${this.id}`, val)
        }
    }
}
</script>
<style lang="scss" scoped>
.select-table-style {
    .icon-select {
        cursor: pointer;
        font-size: 30px;
        transform: scale(0.75);
        opacity: 0.6;
        transition: opacity 0.4s, transform 0.4s;
        &:hover {
            opacity: 0.8;
            transform: scale(0.85);
        }
        &.selected {
            transform: scale(1);
            opacity: 1;
        }
    }
}
</style>