@php
$model_count = $resource->model->count();
$filters = $resource->filters();
$_data =  request()->all();
@endphp
@if ($model_count > 0)
    <div class="row d-flex align-items-end mb-2">
        <div class="col-12 d-flex align-items-end justify-content-start flex-column">
            <div class="w-100 mt-2">
                <resource-filter-tags 
                    id="resource-filter-tags" 
                    ref="tags_filter" :resource_filters="{{ json_encode($filters) }}" 
                    :get_params='@json($_data)'
                >
                </resource-filter-tags>
            </div>
            @php
            $globalFilterData = [
                'filter_route' => route('resource.index',['resource'=>$resource->id]),
                'query' => $_data,
                'value' => @$_data['_'] ? $_data['_'] : '',
            ];
            @endphp
            <div class="d-flex flex-column align-items-start justify-content-between w-100 resource-pagination" data-aos="fade-left">
                @if ($report_mode && $resource->canCreateReportTemplates())
                    <div class="d-flex flex-column w-100">
                        @include("vStack::resources.partials._save_filter_btn")
                    </div>
                @endif
                <div class="d-flex flex-row ml-auto align-items-center f-12 pagination-content">
                    @if ($data->total() > 0)
                        <div class="d-flex flex-row align-items-center justify-content-center pagination-row">
                            {!! $resource->resultsFoundLabel() !!} {{ $data->total() }}
                            <div class="nav-scroller py-1 mb-2"> 
                                <div class="ml-3" id="resource-pagination">
                                    {{ $data->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                    @include("vStack::resources.partials._filter_btn")
                    @if ($resource->search())
                        <resource-filter-global 
                            class="ml-2 mb-2" 
                            :data="{{ json_encode($globalFilterData) }}"
                        >
                        </resource-filter-global>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @if($list_items)
        <resource-list-items :list_items='@json($list_items)'></resource-list-items>        
    @endif
    <div class="row">
        <div class="col-12">
            @if ($data->total() > 0)
                @if ($report_mode)
                    @include("vStack::resources.partials._report_table")
                @else
                    @include("vStack::resources.partials._table")
                @endif
            @else
                @if ($resource->lenses())
                    @include("vStack::resources.partials._lenses")
                @endif
                <h4 class="text-center my-4">
                    {{ $resource->noResultsFoundText() }}
                </h4>
            @endif
        </div>
    </div>

@else
    <div class="d-flex flex-column row align-items-center justify-items-center">
        <div class="col-md-6 col-sm-12 text-center">
            <h4 class="text-center mt-5">
                @if ($resource->icon())
                    <h1 style="opacity: .3;font-size: 250px;"><span class="{{ $resource->icon() }}"></span></h1>
                @endif
                <div>{!! $resource->nothingStoredText() !!}</div>
                @if ($resource->canCreate() || $resource->canImport())
                    <div>{!! $resource->nothingStoredSubText() !!}</div>
                @endif
            </h4>
            @if ($resource->canCreate())
                <resource-store-btn big class="mt-3" resource_id='{{ $resource->id }}' :crud_type='@json($resource->crudType())'
                    label="{{ $resource->storeButtonLabel() }}" route="{{ route('resource.create', ['resource' => $resource->id]) }}">
                </resource-store-btn>
            @endif
        </div>
    </div>
@endif