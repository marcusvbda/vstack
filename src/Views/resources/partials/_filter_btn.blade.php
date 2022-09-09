@php
    $data=[
        "perpage"   => @$_data["per_page"] ? $_data["per_page"] : 10,
        "hasFilter" => ResourcesHelpers::hasFilter(request()->query(),$filters),
        "route"     => $resource->route(),
        "query"     => $_data,
        "filters"   => $filters
    ];
@endphp
<resource-filter-btn class="mb-2" :report_mode='@json($report_mode)' :per_page="{{json_encode($resource->resultsPerPage())}}" :data="{{json_encode($data)}}" label="{{$resource->label()}}"></resource-filter-btn>
