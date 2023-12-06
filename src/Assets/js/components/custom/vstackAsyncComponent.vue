<template>
    <div class="vstack-async-component" v-loading="loading && content">
        <slot v-if="!content"/>
        <v-runtime-template :template="content"/>
    </div>    
</template>
<script>
import VRuntimeTemplate from 'v-runtime-template';
export default {
    props :['payload', 'callback','timeout'],
    components: {
        'v-runtime-template': VRuntimeTemplate,
    },
    data() {
        return {
            loading : true,
            loadingTimeout : this.timeout || 0,
            requestData : {
                payload : this.payload,
                callback : this.callback
            },
            content : null
        }
    },
    created() {
        this.refreshData()
    },
    methods : {
        refreshData() {
            this.loading = true
            setTimeout(() => {
                this.$http.post("/vstack/load-component",this.requestData).then(({data}) => {
                    this.content = `<div>${data.content}</div>`
                }).catch(error => {
                    this.content =  `<div>${error.response.data.content}</div>`
                }).finally(() => {
                    this.loading = false
                })
            },this.loadingTimeout)
        }
    }
}
</script>
<style lang="scss">
.vstack-async-component {
    overflow: hidden;
}
</style>