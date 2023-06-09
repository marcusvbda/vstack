<?php
$globalFilterData = [
    'filter_route' => route('resource.index', ['resource' => $resource->id]),
    'query' => $_data,
    'value' => @$_data['_'] ? $_data['_'] : '',
];
?>
<div class="flex flex-col md:flex-row justify-end items-center gap-5 mt-5">
    <small class="text-sm text-neutral-500">
        <portal-target name="total-count" /></portal-target>
    </small>
    porra da paginacao aqui
    <resource-filter-global :data="{{ json_encode($globalFilterData) }}">
    </resource-filter-global>
</div>