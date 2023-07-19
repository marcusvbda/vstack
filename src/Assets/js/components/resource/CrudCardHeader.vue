<template>
    <div class="card-header crud-card-header">
        <div class="row-responsive-table w-full">
            <table class="table-crud">
                <tbody>
                    <tr>
                        <td>
                            <div>
                                <b
                                    v-if="label"
                                    v-html="label"
                                    class="text-neutral-700 dark:text-neutral-300"
                                />
                                <small
                                    v-if="description"
                                    v-html="description"
                                />
                            </div>
                        </td>
                        <td>
                            <VRuntimeTemplate
                                v-if="append_content"
                                :template="append_content"
                            />
                            <slot v-else />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<script>
import VRuntimeTemplate from 'v-runtime-template';

export default {
    props: ['label', 'description', 'form', 'append_content'],

    components: {
        VRuntimeTemplate,
    },
};
</script>
