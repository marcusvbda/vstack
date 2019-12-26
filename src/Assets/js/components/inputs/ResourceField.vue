<template>
    <div>
        <div v-html="html"></div>
    </div>
</template>
<script>
export default {
    props : ["resource","params"],
    data() {
        return {
            resourceRoute : null,
            resourceName : null,
            html : null,
        }
    },
    async created() {
        this.loadView()
    },
    methods : {
        loadView() {
            this.resourceName = this.resource.charAt(0).toLowerCase()+this.resource.slice(1)
            this.resourceName = this.resourceName.replace( /([A-Z])/g, " $1" ).split(' ').join('-').toLowerCase()
            this.resourceRoute = laravel.vstack.resource_field_route.replace("%%resource%%",this.resourceName)
            let params = this.params
            params["redirect_back"] = window.location.href
            this.$http.post(this.resourceRoute,params).then(res=>{
                this.html = res.data
            })
        }
    }
}
</script>