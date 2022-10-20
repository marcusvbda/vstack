<template>
    <table class="table table-crud mb-0">
        <RuntimeTemplateCompiler v-for="(field,i) in fields" :key="i" :template="field.view.unescape()" />
    </table>
</template>

<script>
import { RuntimeTemplateCompiler } from "vue-runtime-template-compiler"

export default {
    props: ["fields"],
    data() {
        return {
            form: {},
            errors: {},
        }
    },
    components: {
        RuntimeTemplateCompiler
    },
}
</script>