@extends("templates.admin")
@section('title',"Cards Customizados")
@section('breadcrumb')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.home')}}" class="link">Dashboard</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="{{$resource->route()}}">{{$resource->label()}}</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="{{$resource->route()."/custom-cards"}}">Cards Customizados</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$data["page_type"]}} de Cards Customizados</li>
                </ol>
            </nav>
        </nav>
    </div>
</div>
@endsection
@section('content')
<div class="row mb-3 mt-2">
    <div class="col-12 d-flex flex-row align-items-center flex-wrap">
        <h4 class="mb-1"><b class="el-icon-data-line mr-2"></b>{{$data["page_type"]}} de Cards Customizados</h4> 
    </div>
</div>

<resource-metric-crud 
    @if(@$card) :card="{{json_encode($card)}}" @endif
    resourceroute="{{$resource->route()}}"
    :custommetricoptions="{{json_encode($resource->customMetricOptions())}}"
    >
</resource-metric-crud> 

@endsection
