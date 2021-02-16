<template>
    <div :id="label">
        <div class="card mb-4 p-0">
            <div class="card-header crud-card-header px-3">
                <div class="row">
                    <div class="col-12 d-flex align-items-center justify-content-between">
                        <div><b v-html="label" class="card-title" /></div>
                        <div v-if="advanced">
                            <el-checkbox v-model="showing">Mostrar Informações Avançadas</el-checkbox>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="row" v-if="real_showing">
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
