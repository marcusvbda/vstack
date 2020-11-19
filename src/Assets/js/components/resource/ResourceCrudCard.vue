<template>
    <div :id="label">
        <div class="card mb-4">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-12 d-flex align-items-center justify-content-between">
                        <div><h4 v-html="label" /></div>
                        <div v-if="advanced">
                            <el-switch v-model="showing" active-text="Informações Avançadas" />
                        </div>
                    </div>
                </div>

                <div class="row mb-3" v-if="real_showing">
                    <div class="col-12">
                        <slot />
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import VRuntimeTemplate from 'v-runtime-template'
export default {
    props: ['label', 'advanced', 'inputs'],
    data: () => ({
        showing: false,
    }),
    components: {
        'v-runtime-template': VRuntimeTemplate,
    },
    computed: {
        real_showing() {
            if (!this.advanced) return true
            return this.showing
        },
    },
}
</script>