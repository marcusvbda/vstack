<div class="flex flex-col mt-4">
    <resource-filter-tags id="resource-filter-tags" ref="tags_filter" :resource_filters="{{ json_encode($filters) }}"
        :get_params='@json($_data)'>
    </resource-filter-tags>
    @php
        $routeParam = ['resource' => $resource->id];
        $globalFilterData = [
            'filter_route' => @$report_mode ? route('resource.report', $routeParam) : route('resource.index', $routeParam),
            'query' => $_data,
            'value' => @$_data['_'] ? $_data['_'] : '',
        ];
    @endphp
    <div class="flex flex-col md:flex-row justify-end items-center gap-5 mt-5">
        @if ($resource->search())
            <resource-filter-global :data='@json($globalFilterData)'>
            </resource-filter-global>
        @endif
        @include('vStack::resources.partials._filter_btn')
    </div>
</div>
