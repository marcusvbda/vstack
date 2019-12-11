<template>
    <div class="h-100" v-loading="loading">
        <div class='d-flex flex-row justify-content-between align-items-center mb-2'>
            <slot name='label'></slot>
            <slot name='sublabel'></slot>
        </div>
        <div v-html="content" ref="content" style="display:none;"></div>
    </div>
</template>
<script>
export default {
    props : ["route","time"],
    data() {
        return {
            content : null,
            loading : false
        }
    },
    async created() {
        this.updateData()
        setInterval(_ => {
            this.updateData()
        },this.time*1000)
    },
    methods : {
        updateData() {
            this.loading = true
            this.$http.post(this.route).then( res => {
                this.content = res.data
                $(this.$refs.content).show()
                this.loading = false
            }).catch(er => {
                console.log(er)
                this.loading = false
            })
        }
    }
}
</script>