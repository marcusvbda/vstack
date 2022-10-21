<template>
    <table class="table table-crud mb-0">
        <VRuntimeTemplate v-for="(field,i) in fields" :key="i" :template="field.view.unescape()" />
    </table>
</template>

<script>
import VRuntimeTemplate from "v-runtime-template";

export default {
    props: ["fields", "errors"],
    data() {
        return {
            form: {},
        }
    },
    components: {
        VRuntimeTemplate
    },
    watch: {
        errors: {
            handler(val) {
                this.makeFormValidationErrors(val)
            },
            deep: true
        }
    },
    methods: {
        makeFormValidationErrors(errors) {
            try {
                let message = Object.keys(errors)
                    .map((key) => `<li>${errors[key][0]}</li>`)
                    .join("");
                this.$message({
                    dangerouslyUseHTMLString: true,
                    showClose: true,
                    message: `<ul>${message}</ul>`,
                    type: "error",
                });
            } catch {
                return;
            }
        },
    }
}
</script>