<template>
    <div :id="label" :index="index">
        <div class="vstack-crud-card">
            <CrudCardHeader
                :label="label"
                :description="description"
                :form="form"
                :append_content="append_content"
            >
                 <portal-target
                    :name="`portal-card-header-${label.toSlug()}`"
                    :id="`portal-card-header-${label.toSlug()}`"
                />
            </CrudCardHeader>
            <div class="crud-card-body">
                <div class="row">
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
    props: [
        'label',
        'description',
        'inputs',
        'index',
        'append_content',
        'form',
    ],
};
</script>
