<div class="row mb-3 mt-2">
    <div class="col-12 d-flex flex-row align-items-center sm-flex-wrap" data-aos="fade-left">
        <h4 class="mb-1">
            @if (!$report_mode)
                {!! @$resource->indexLabel() !!}
            @else
                {!! @$resource->reportLabel() !!}
            @endif
        </h4>
        <div class="d-flex flex-row  flex-wrap align-items-center sm-w-100 sm-mt-4">
            @if (!$report_mode)
                @if ($resource->canCreate())
                    @if ($resource->model->count() > 0)
                        <resource-store-btn class="resource-btn" resource_id='{{ $resource->id }}' :crud_type='@json($resource->crudType())'
                            label="{{ $resource->storeButtonLabel() }}" route="{{ route('resource.create', ['resource' => $resource->id]) }}">
                        </resource-store-btn>
                    @endif
                @endif                
                @if ($resource->canImport())
                    <a class="ml-2 mr-4 f-12 link" href="{{ route('resource.import', ['resource' => $resource->id]) }}">
                        {!! $resource->importButtonlabel() !!}
                    </a>
                @endif
            @endif
            @if ($report_mode)
                @if ($resource->canExport() && $data->total() > 0)
                    @if ($resource->model->count() > 0)
                        @php
                            $resource_config_query = \marcusvbda\vstack\Models\ResourceConfig::where('data->user_id', $user->id)->where('resource', $resource->id);
                            $config_columns = (clone $resource_config_query)->where('config', 'resource_export_disabled_columns')->first();
                            $config_columns = $config_columns ? $config_columns : [];
                            $report_export_query = (clone $resource_config_query)->where('config', 'like', 'report_export_%');
                        @endphp
                        <resource-export-btn class="ml-4 link f-12" id="{{ $resource->id }}" label="{{ $resource->label() }}" 
                            :export_columns='@json($resource->exportColumns())' 
                            :get_params='@json($_GET)' :qty_results='@json($data->total())' 
                            :config_columns='@json($config_columns)' 
                            message='{{ $resource->exportingMessage() }}'
                        >
                            {!! $resource->exportButtonlabel() !!}
                        </resource-export-btn>
                    @endif
                @endif
            @else
                @if ($resource->canViewReport())
                    <a class="ml-2 f-12" href="/admin/relatorios/{{ $resource->id }}">
                        <span class="el-icon-tickets mr-1"></span>
                        Relatório de {{ $resource->label() }}
                    </a>
                @endif
            @endif
        </div>
    </div>
</div>


<div class="w-100" data-aos="fade-up">
    @if ($report_mode)
        @if (@$resource->beforeReportListSlot())
            {!! @$resource->beforeReportListSlot() !!}
        @endif
    @else
        @if (@$resource->beforeListSlot())
            {!! @$resource->beforeListSlot() !!}
        @endif
    @endif
</div>
<?php
$model_count = $resource->model->count();
$filters = $resource->filters();
?>
@if ($model_count > 0)
    <div class="row d-flex align-items-end mb-2">
        <div class="col-12 d-flex align-items-end justify-content-start flex-column">
            <div class="w-100 mt-2">
                <resource-filter-tags ref="tags_filter" :resource_filters="{{ json_encode($filters) }}" :get_params="{{ json_encode($_GET) }}">
                </resource-filter-tags>
            </div>
            <?php
            $globalFilterData = [
                'filter_route' => request()->url(),
                'query' => request()->query(),
                'value' => @$_GET['_'] ? $_GET['_'] : '',
            ];
            ?>
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
                                <div class="ml-3">
                                    {{ $data->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                    @include("vStack::resources.partials._filter_btn")
                    @if ($resource->search())
                        <resource-filter-global class="ml-2 mb-2" :data="{{ json_encode($globalFilterData) }}"></resource-filter-global>
                    @endif
                </div>
            </div>
        </div>
    </div>
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
                <h4 class="text-center my-4">{{ $resource->noResultsFoundText() }}</h4>
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
@if (@$resource->afterListSlot())
    <div class="mt-3">
        {!! @$resource->afterListSlot() !!}
    </div>
@endif
@endsection
