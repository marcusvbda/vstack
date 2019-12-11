<template>
    <div class="row d-flex flex-wrap flex-row h-100">
        <template v-for="(metric,i) in metrics">
            <v-runtime-template :key="i" :template="metric.view" />
        </template>
    </div>
</template>
<script>
import VRuntimeTemplate from "v-runtime-template"
export default {
    props:["metrics"],
    components : {
        "v-runtime-template" : VRuntimeTemplate
    },
}
</script>