<?php
$crud_buttons = [
    "code"         => null,
    "can_view"     => $resource->canView(),
    "can_update"   => $resource->canUpdate(),
    "can_delete"   => $resource->canDelete(),
    "route"        => null
];

$list_types = @$resource->listType() ? $resource->listType() : ["table"];
$table = $resource->exportColumns();
$table_keys = array_keys($table);
$controller  = new  \marcusvbda\vstack\Controllers\VstackController;
?>
<div class="card" data-aos="fade-right" >
	<div class="card-body p-0">
		<div class="table-responsive"  >
			<table class="table table-sm table-hover mb-0">
				<thead class="thead-dark">
					<tr>
						@foreach($table as $key=>$value)
							<th style="min-width: 200px;">
								@if(@$value["label"]) 
									{{ @$value["label"] }} 
								@else 
									{{  $key }} 
								@endif
							</th>
						@endforeach
					</tr>
				</thead>
				<tbody>
					@foreach($data as $row)
						<tr id="{{ $row->id }}">
							@foreach($table_keys as $key)
								<td>
									{{ $controller->getColumnIndex($table,$resource->model->find($row->id),$key) }}
								</td>
							@endforeach
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>