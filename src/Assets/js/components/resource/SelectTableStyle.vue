<template>
    <div style="display:none;" ref="container" class="card select-table-style">
        <div
            class="card-header p-1"
            v-if="list_type.includes('cards') && list_type.includes('table') && hasLenses"
        >
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
                            @click="selectType('cards')"
                            :class="`${selected_list_type=='cards' ? 'selected' : ''} el-icon-s-grid icon-select`"
                        />
                        <span
                            @click="selectType('table')"
                            :class="`${selected_list_type=='table' ? 'selected' : ''} el-icon-s-unfold mr-1 icon-select`"
                        />
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <slot name="content" />
        </div>
    </div>
</template>
<script>
export default {
    props: ["id", "list_type", "selected_list_type"],
    data() {
        return {
            initalized: false,
        }
    },
    computed: {
        hasLenses() {
            return this.$slots.lenses ? true : false
        }
    },
    mounted() {
        this.$refs.container.style.display = 'block'
        this.initialized = true
    },
    methods: {
        selectType(type) {
            if (this.selected_list_type == type) return
            if (history.pushState) {
                let searchParams = new URLSearchParams(window.location.search)
                searchParams.set('list_type', type)
                let newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?' + searchParams.toString()
                window.history.pushState({ path: newurl }, '', newurl)
                window.location.reload()
                this.$loading({ text: "Carregando Visualização..." })
            }
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
