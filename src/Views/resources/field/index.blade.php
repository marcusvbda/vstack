<div class="row mb-3 mt-2">
    <div class="col-12 d-flex flex-row align-items-center">
        <h4 class="mb-1">{!! @$resource->indexLabel() !!}</h4>
        <div class="d-flex flex-row  flex-wrap align-items-center">
            @if($resource->canCreate())
                @if($resource->model->count()>0)
                    <a class="btn btn-primary btn-sm btn-sm-block cursor-pointer px-3 pr-2 mx-4 mb-1" 
                        href="{{route('resource.create',['resource'=>$resource->id,'params'=>$params])}}">
                        {!! $resource->storeButtonLabel() !!}
                    </a>
                @endif
            @endif
        </div>
    </div>
</div>
@if($resource->model->count()>0)
    <div class="row">
        <div class="col-12">
            @if($data->count()>0)
                @include("vStack::resources.field._table")
            @else 
                <h4 class="text-center my-4">{{ $resource->noResultsFoundText() }}</h4>
            @endif
        </div>
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
                <a class="btn btn-primary btn-sm-block cursor-pointer mb-3 mt-3" href="{{route('resource.create',['resource'=>$resource->id,'params'=>$params])}}">
                    {!! $resource->storeButtonLabel() !!}
                </a>
            @endif
        </div>
    </div>
@endif    