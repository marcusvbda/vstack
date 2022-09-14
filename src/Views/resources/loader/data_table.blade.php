<resource-list-items resource_id="{{ $resource->id }}" :request_data='@json(request()->all())'></resource-list-items>   
<div class="row">
    <div class="col-12">
        @if ($report_mode)
            @include("vStack::resources.partials._report_table")
        @else
            @include("vStack::resources.partials._table")
        @endif
    </div>
</div> 
    
