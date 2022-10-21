<template>
    <div class="row mb-3 card-collapse">
        <div class="col-12">
            <el-collapse v-model="active">
                <el-collapse-item :disabled="disabled">
                    <template slot="title">
                        <slot name="header" />
                    </template>
                    <slot name="body" />
                </el-collapse-item>
            </el-collapse>
        </div>
    </div>
</template>
<script>

export default {
    props: {
        disabled: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            active: null
        }
    }
}
</script>

<style lang="scss">
.card-collapse {
    // 
}
</style>