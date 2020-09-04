<?php
    $filters = $resource->filters();
    $data=[
        "perpage"   => @$_GET["per_page"] ? $_GET["per_page"] : 10,
        "hasFilter" => ResourcesHelpers::hasFilter(request()->query(),$filters),
        "route"     => $resource->route(),
        "query"     => request()->query(),
        "filters"   => $filters
    ];
?>
<resource-filter-btn :data="{{json_encode($data)}}" label="{{$resource->label()}}"></resource-filter-btn>