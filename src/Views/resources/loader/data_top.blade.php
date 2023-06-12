<div class="flex flex-col mt-4">
    <resource-filter-tags id="resource-filter-tags" ref="tags_filter" :resource_filters="{{ json_encode($filters) }}" :get_params='@json($_data)'>
    </resource-filter-tags>
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
        @if ($resource->search())
        <resource-filter-global :data="{{ json_encode($globalFilterData) }}">
        </resource-filter-global>
        @endif
        @include("vStack::resources.partials._filter_btn")
    </div>
    <div class="vstack-pagination">
        {{ $data->appends($_data)->links() }}
    </div>
</div>