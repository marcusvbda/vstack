@extends("templates.admin")
@section('title',$resource->label())
@section('breadcrumb')
@include("vStack::resources.partials._breadcrumb")
@endsection
@section('content')
<?php $user = Auth::user(); ?>
<div class="row mb-3 mt-2">
    <div class="col-12 d-flex flex-row align-items-center">
        <h4 class="mb-1">
			@if(!$report_mode)
				{!! @$resource->indexLabel() !!}
			@else
				{!! @$resource->reportLabel() !!}
			@endif
		</h4>
        <div class="d-flex flex-row  flex-wrap align-items-center">
			@if(!$report_mode)
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
			@endif
			@if($report_mode)
				@if($resource->canExport())
					@if($resource->model->count()>0)
						<?php
							$resource_config_query = \marcusvbda\vstack\Models\ResourceConfig::where("data->user_id", $user->id)->where("resource", $resource->id);
							$config_columns = (clone $resource_config_query)->where("config", "resource_export_disabled_columns")->first();
							$config_columns = $config_columns ? $config_columns : [];
							$report_export_query = (clone $resource_config_query)->where("config","like","report_export_%");
							$exports =  (clone $report_export_query)->get();
							$waiting_qty =  (clone $report_export_query)->count();
							$waiting_exporting_qty = (clone $report_export_query)->where("data->status","exporting")->count();
						?>
						<resource-export-btn class="ml-2 link" 
							id="{{$resource->id}}" 
							label="{{$resource->label()}}"
							:export_columns='@json($resource->export_columns())' 
							:get_params='@json($_GET)'
							:qty_results='@json($data->total())' 
							:limit='@json($resource->maxRowsExportSync())'
							:config_columns='@json($config_columns)'
							:waiting_qty='@json($waiting_exporting_qty)'
							:waiting_limit='@json($resource->maxWaitingReportsByUser())'
						>
							{!! $resource->exportButtonlabel() !!}
						</resource-export-btn>
					@endif
				@endif
			@else
				@if($resource->canViewReport())
					<a class="ml-2" href="/admin/relatorios/{{ $resource->id }}">
					<span class="el-icon-tickets mr-1"></span>Relatório de {{ $resource->label() }}</a>
				@endif
			@endif
        </div>
    </div>
</div>


@if($report_mode)
	@if(@$resource->beforeReportListSlot())
		{!! @$resource->beforeReportListSlot() !!}
	@endif
@else
	@if(@$resource->beforeListSlot())
		{!! @$resource->beforeListSlot() !!}
	@endif
@endif
<?php 
    $model_count = $resource->model->count();
    $filters = $resource->filters();
?>
@if($model_count>0)
<div class="row d-flex align-items-end mb-2">
    <div class="col-12 d-flex align-items-end justify-content-start flex-column">
        <div class="w-100 mt-2">
			<resource-filter-tags ref="tags_filter" :resource_filters="{{json_encode($filters)}}"
				:get_params="{{json_encode($_GET)}}">
			</resource-filter-tags>
		</div>
        <?php 
            $globalFilterData = [
                "filter_route" => request()->url(),
                "query" => request()->query(),
                "value" => @$_GET["_"] ? $_GET["_"] : ""
            ];
        ?>
        <div class="d-flex flex-column align-items-start justify-content-between w-100">
			<div class="d-flex flex-column w-100">
				@if($report_mode && $resource->canCreateReportTemplates())
					@include("vStack::resources.partials._save_filter_btn")
				@endif
			</div>
            <div class="d-flex flex-row ml-auto align-items-center">
				@if($data->total()>0)
					<div class="d-flex flex-row align-items-center justify-content-center">
						{!! $resource->resultsFoundLabel() !!} {{ $data->total() }}
						<div class="ml-3">{{$data->appends(request()->query())->links()}}</div>
					</div>
				@endif
				@include("vStack::resources.partials._filter_btn")
				@if($resource->search())
					<resource-filter-global class="ml-2" :data="{{json_encode($globalFilterData)}}"></resource-filter-global>
				@endif
			</div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12">
		@if($data->total()>0)
			@if($report_mode)
				@include("vStack::resources.partials._report_table")
			@else
				@include("vStack::resources.partials._table")
			@endif
        @else
        @if($resource->lenses())
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