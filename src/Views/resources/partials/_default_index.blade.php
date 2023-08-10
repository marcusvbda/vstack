@php
    use marcusvbda\vstack\Models\ResourceConfig;
    $user = Auth::user();
    $hash = '';
@endphp
@if (!@$only_table)
    <div class="flex flex-col">
        <div class="flex flex-col md:flex-row items-center gap-5">
            <h4 class="text-3xl md:text-2xl font-bold text-neutral-800 dark:text-neutral-200" id="resource-label">
                {!! @$report_mode ? @$resource->reportLabel() : @$resource->indexLabel() !!}
            </h4>
            @if (!@$report_mode && $resource->canCreate())
                <resource-store-btn resource_id='{{ $resource->id }}' label="{{ $resource->storeButtonLabel() }}"
                    route="{{ route('resource.create', ['resource' => $resource->id]) }}">
                </resource-store-btn>
            @endif
            @if ($resource->canImport() && !@$report_mode && $resource->canCreate())
                <a id="resource-import-link" class="vstack-link"
                    href="{{ route('resource.import', ['resource' => $resource->id]) }}">
                    {!! $resource->importButtonlabel() !!}
                </a>
            @endif
            @if (@$report_mode && $resource->canExport() && $resource->model->toBase()->first('id'))
                @php
                    $resource_config_query = ResourceConfig::where('data->user_id', $user->id)->where('resource', $resource->id);
                    $config_columns = (clone $resource_config_query)->where('config', 'resource_export_disabled_columns')->first();
                    $config_columns = $config_columns ? $config_columns : [];
                    $report_export_query = (clone $resource_config_query)->where('config', 'like', 'report_export_%');
                    $total = (@$data && @$data->total()) || 0;
                @endphp
                <resource-export-btn id="{{ $resource->id }}" label="{{ $resource->label() }}"
                    :export_columns='@json($resource->exportColumns())' :get_params='@json($_GET)'
                    :qty_results='@json($total)' :config_columns='@json($config_columns)'
                    message='{{ $resource->exportingMessage() }}'>
                    {!! $resource->exportButtonlabel() !!}
                </resource-export-btn>
            @endif
            @if (!@$report_mode && $resource->canViewReport())
                <a class="ml-2 f-12 vstack-link" href="/admin/relatorios/{{ $resource->id }}" id="resource-report-btn">
                    <span class="el-icon-tickets mr-1"></span>
                    Relatório de {{ $resource->label() }}
                </a>
            @endif
        </div>
    </div>
    <div class="w-full">
        @if (@$report_mode && @$resource->beforeReportListSlot())
            {!! @$resource->beforeReportListSlot() !!}
        @endif
        @if (!@$report_mode && @$resource->beforeListSlot())
            {!! @$resource->beforeListSlot() !!}
        @endif
@endif
@if (@$related_resource)
    @if ($resource->canCreate() && @$raw_type == 'edit')
        @php
            $related_resource_id = $related_resource_id;
            $hash = base64_encode(
                json_encode([
                    'resource_id' => $resource->id,
                    'related_resource_id' => $related_resource_id,
                    'related_resource' => $related_resource,
                    'redirect_url' => url()->current(),
                ]),
            );
        @endphp
        <portal to="portal-card-header-{{ $resource->labelSlug() }}">
            <div class="w-full flex justify-end">
                <resource-store-btn resource_id='{{ $resource->id }}' label="{{ $resource->storeButtonLabel() }}"
                    route="{{ route('resource.create', ['resource' => $resource->id, 'hash' => $hash]) }}">
                </resource-store-btn>
            </div>
        </portal>
    @endif
@endif

@php
    $cursor = @request()?->cursor ? request()->cursor : '';
    $extra_filters = @$extra_filters ? $extra_filters : [];
    $related_resource = @$related_resource ? $related_resource : '';
    $related_resource_id = @$related_resource_id ? $related_resource_id : '';
@endphp

<resource-index-loader hash="{{ $hash }}" raw_type="{{ @$raw_type ? $raw_type : 'list' }}"
    :only_table='@json(@$only_table ? true : false)' :report_mode='@json($report_mode)'
    :extra_filters='@json($extra_filters)' related_resource='{{ $related_resource }}'
    related_resource_id='{{ $related_resource_id }}' resource_id='{{ $resource->id }}' cursor="{{ $cursor }}">
    <resource-list-items resource_id="{{ $resource->id }}" :request_data='@json(request()->all())'>
    </resource-list-items>
</resource-index-loader>

@include('vStack::resources.loader.list_shimmer')

@if (!@$only_table)
    @if (@$report_mode && @$resource->afterReportListSlot())
        {!! @$resource->afterReportListSlot() !!}
    @endif
    @if (!@$report_mode && @$resource->afterListSlot())
        <div class="mt-3">
            {!! @$resource->afterListSlot() !!}
        </div>
    @endif
    </div>
@endif
