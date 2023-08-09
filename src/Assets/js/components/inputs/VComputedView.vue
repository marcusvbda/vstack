<template>
    <CustomResourceComponent :label="label" :description="description">
        <div class="d-flex flex-column">
            <slot name="prepend-slot" />
            <VRuntimeTemplate :template="computed_value" />
            <slot name="append-slot" />
        </div>
    </CustomResourceComponent>
</template>
<script>
import VRuntimeTemplate from 'v-runtime-template';
export default {
    props: ['label', 'description', 'eval_script', 'form', 'template_script'],
    components: {
        VRuntimeTemplate,
    },
    computed: {
        computed_value() {
            const script = atob(this.eval_script ? this.eval_script : '');
            const template_script = this.template_script
                ? atob(this.template_script)
                : '<span>{{eval}}</span>';

            const value = eval(script);
            const content = template_script.replace('{{eval}}', value);
            return content;
        },
    },
};
</script>
