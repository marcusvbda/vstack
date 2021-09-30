<template>
    <div :id="label" :index="index">
        <div class="card mb-4 p-0">
            <div class="card-header crud-card-header">
                <div class="row">
                    <div class="col-12 d-flex align-items-center justify-content-between">
                        <div><b v-html="label" class="card-title" /></div>
                        <div v-if="advanced">
                            <el-checkbox v-model="showing" class="mb-1">Visualizar Informações</el-checkbox>
                        </div>
                        <portal-target :name="`card-header-right-${index}`" />
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
export default {
    props: ["label", "advanced", "inputs", "index"],
    data: () => ({
        showing: false
    }),
    computed: {
        real_showing() {
            if (!this.advanced) return true;
            return this.showing;
        }
    }
};
</script>
