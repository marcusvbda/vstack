@extends("templates.admin")
@section('title',$resource->label())
@section('content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.home')}}" class="link">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{$resource->label()}}</li>
                </ol>
            </nav>
        </nav>
    </div>
</div>

@include("vStack::resources.partials._metrics")

<div class="row mb-3 mt-2">
    <div class="col-12 d-flex flex-row align-items-center">
        <h4 class="mb-1">{!! @$resource->indexLabel() !!}</h4>
        <div class="d-flex flex-row">
            @if($resource->canCreate())
                <div class="ml-4">
                    @if($resource->model->count()>0)
                        <a class="btn btn-primary btn-sm-block btn-sm cursor-pointer px-3 pr-4" href="{{route('resource.create',['resource'=>$resource->id])}}">
                            {!! $resource->storeButtonLabel() !!}
                        </a>
                    @endif
                </div>
                @if($resource->canImport())
                    <a class="btn btn-outline-secondary btn-sm-block btn-sm cursor-pointer pr-3 ml-2" href="{{route('resource.import',['resource'=>$resource->id])}}">
                        {!! $resource->importButtonlabel() !!}
                    </a>
                @endif
            @endif
            @if($resource->canView() && $resource->canExport())
                @if($resource->model->count()>0)
                    <a class="btn btn-outline-secondary btn-sm-block btn-sm cursor-pointer pr-3 ml-2" target="_BLANK" href="{{route('resource.export',array_merge(request()->all(),['resource'=>$resource->id]))}}">
                        {!! $resource->exportButtonlabel() !!}
                    </a>
                @endif
            @endif
        </div>
    </div>
</div>

@if($resource->model->count()>0)
    @include("vStack::resources.partials._filter")
    <div class="row d-flex align-items-end mb-2">
        <div class="col-md-9 col-sm-12">
            @if($resource->lenses())
                @include("vStack::resources.partials._lenses")
            @endif
        </div>
        <div class="col-md-3 col-sm-12">
            <?php 
                $globalFilterData = [
                    "filter_route" => request()->url(),
                    "query" => request()->query(),
                    "value" => @$_GET["_"] ? $_GET["_"] : ""
                ];
            ?>
            @if($resource->model->count()>0)
                @if($resource->search())
                    <resource-filter-global :data="{{json_encode($globalFilterData)}}"></resource-filter-global>
                @endif
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @if($data->total()>0)
                @include("vStack::resources.partials._table")
            @else 
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
                <h1 style="opacity: .3;font-size: 250px;">{!!$resource->icon()!!}</h1>
                <div>{!! $resource->nothingStoredText() !!}</div>
                <div>{!! $resource->nothingStoredSubText() !!}</div>
            </h4>
            @if($resource->canCreate())
                <a class="btn btn-primary btn-sm-block cursor-pointer mb-3 mt-3" href="{{route('resource.create',['resource'=>$resource->id])}}">
                    {!! $resource->storeButtonLabel() !!}
                </a>
            @endif
        </div>
    </div>
@endif    
@endsection