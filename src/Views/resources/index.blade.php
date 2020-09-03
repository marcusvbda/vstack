@extends("templates.admin")
@section('title',$resource->label())
@section('breadcrumb')
@include("vStack::resources.partials._breadcrumb")
@endsection
@section('content')
@if(@$resource->beforeListSlot())
{!! @$resource->beforeListSlot() !!}
@endif
<div class="row mb-3 mt-2">
    <div class="col-12 d-flex flex-row align-items-center">
        <h4 class="mb-1">{!! @$resource->indexLabel() !!}</h4>
        <div class="d-flex flex-row  flex-wrap align-items-center">
            @if($resource->canCreate())
            @if($resource->model->count()>0)
            <a class="btn btn-primary btn-sm btn-sm-block cursor-pointer px-3 pr-2 mx-4 mb-1"
                href="{{route('resource.create',['resource'=>$resource->id])}}">
                {!! $resource->storeButtonLabel() !!}
            </a>
            @endif
            @if($resource->canImport())
            <a class="ml-2 link" href="{{route('resource.import',['resource'=>$resource->id])}}">
                {!! $resource->importButtonlabel() !!}
            </a>
            @endif
            @endif
            @if($resource->canExport())
            @if($resource->model->count()>0)
            <resource-export-btn class="ml-2 link" id="{{$resource->id}}" label="{{$resource->label()}}"
                :export_columns="{{json_encode($resource->export_columns())}}" :get_params="{{json_encode($_GET)}}"
                :qty_results="{{json_encode($data->total())}}" :limit="{{json_encode($resource->maxRowsExportSync())}}">
                {!! $resource->exportButtonlabel() !!}
            </resource-export-btn>
            @endif
            @endif
        </div>
    </div>
</div>
<?php 
    $model_count = $resource->model->count();
?>
@if($model_count>0)
@if(!@$resource->groupFilters())
@include("vStack::resources.partials._filter")
@endif
<div class="row d-flex align-items-end mb-2">
    <div class="col-md-9 col-sm-12"></div>
    <div class="col-md-3 col-sm-12 d-flex justify-content-end align-items-center">
        <?php 
            $globalFilterData = [
                "filter_route" => request()->url(),
                "query" => request()->query(),
                "value" => @$_GET["_"] ? $_GET["_"] : ""
            ];
        ?>
        @if(@$resource->groupFilters())
        @include("vStack::resources.partials._filter_btn")
        @endif
        @if($resource->search())
        <resource-filter-global class="ml-2" :data="{{json_encode($globalFilterData)}}"></resource-filter-global>
        @endif
    </div>
</div>
<div class="row">
    <div class="col-12">
        @if($data->total()>0)
        @include("vStack::resources.partials._table")
        @else
        @if($resource->lenses())
        @include("vStack::resources.partials._lenses")
        @endif
        <h4 class="text-center my-4">{{ $resource->noResultsFoundText() }}</h4>
        @endif
    </div>
</div>
<div class="row mt-3 d-flex align-items-center">
    @if($data->total()>0)
    <div class="col-md-6 col-sm-12">{!! $resource->resultsFoundLabel() !!} {{ $data->total() }}</div>
    <div class="col-md-6 col-sm-12 d-flex justify-content-end">
        {{$data->appends(request()->query())->links()}}
    </div>
    @endif
</div>
@else
<div class="d-flex flex-column row align-items-center justify-items-center">
    <div class="col-md-6 col-sm-12 text-center">
        <h4 class="text-center mt-5">
            @if($resource->icon())
            <h1 style="opacity: .3;font-size: 250px;"><span class="{{$resource->icon()}}"></span></h1>
            @endif
            <div>{!! $resource->nothingStoredText() !!}</div>
            <div>{!! $resource->nothingStoredSubText() !!}</div>
        </h4>
        @if($resource->canCreate())
        <a class="btn btn-primary btn-sm-block cursor-pointer mb-3 mt-3"
            href="{{route('resource.create',['resource'=>$resource->id])}}">
            {!! $resource->storeButtonLabel() !!}
        </a>
        @endif
    </div>
</div>
@endif
@if(@$resource->afterListSlot())
<div class="mt-3">
    {!! @$resource->afterListSlot() !!}
</div>
@endif
@endsection