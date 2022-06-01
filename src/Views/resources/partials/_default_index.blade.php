<div class="row mb-3 mt-2">
    <div class="col-12 d-flex flex-row align-items-center sm-flex-wrap" data-aos="fade-left">
        <h4 class="mb-1" id="resource-label">
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
                    <a class="ml-2 mr-4 f-12 link" id="resource-import-link" href="{{ route('resource.import', ['resource' => $resource->id]) }}">
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
                    <a class="ml-2 f-12" href="/admin/relatorios/{{ $resource->id }}" id="resource-report-btn">
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
<resource-index-loader
    resource_id='{{ $resource->id }}'
>
</resource-index-loader>
@if (@$resource->afterListSlot())
    <div class="mt-3">
        {!! @$resource->afterListSlot() !!}
    </div>
@endif
@endsection
