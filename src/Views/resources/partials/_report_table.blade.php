<?php
$crud_buttons = [
    "code"         => null,
    "can_view"     => $resource->canView(),
    "can_update"   => $resource->canUpdate(),
    "can_delete"   => $resource->canDelete(),
    "route"        => null
];

$list_types = @$resource->listType() ? $resource->listType() : ["table"];
$table = $resource->export_columns();
$table_keys = array_keys($table);
$exports = \marcusvbda\vstack\Models\ResourceConfig::where("data->user_id", Auth::user()->id)
			->where("resource", $resource->id)->where("config","like","report_export_%")
			->get();
?>
<div class="card">
	<div class="card-header p-0">
		<div class="col-md-9 col-sm-12">
			@if($resource->lenses())
				@include("vStack::resources.partials._lenses")
			@endif
		</div>
	</div>
	<div class="card-body p-0">
		<div class="table-responsive">
			<table class="table table-sm table-hover mb-0">
				<thead class="thead-dark">
					<tr>
						@foreach($table as $key=>$value)
							<th style="min-width: 200px;">@if(@$value["label"]) {{ $value["label"] }} @else {{  $key }} @endif</th>
						@endforeach
					</tr>
				</thead>
				<tbody>
					@foreach($data as $row)
						<tr>
							@foreach($table_keys as $key)
								<td>
									{{ $resource->getColumnIndex($table,$resource->model->find($row->id),$key) }}
								</td>
							@endforeach
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>



@if($exports->count() > 0)
	<resource-export-list
		:report_exports='@json($exports)'
	>
	</resource-export-list>
@endif