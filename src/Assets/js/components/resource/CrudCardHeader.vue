<template>
    <div class="card-header crud-card-header d-flex align-items-center p-0">
        <div class="row-responsive-table w-100">
            <table class="table table-crud mb-0">
                <tbody>
                    <tr id="resource-input-text-video_id" class="mb-3">
                        <td class="w-25">
                            <div class="d-flex flex-column">
                                <b class="card-title montserrat mb-0 header-title" v-if="label" v-html="label" />
                                <small class="mt-1 text-muted" v-if="description" v-html="description" />
                            </div>
                        </td>
                        <td>
                            <VRuntimeTemplate v-if="append_content" :template="append_content" />
                            <slot v-else />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
<script>
import VRuntimeTemplate from "v-runtime-template";

export default {
    props: ["label", "description", "form", "append_content"],

    components: {
        VRuntimeTemplate
    },
}
</script>