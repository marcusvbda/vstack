<div class="card">
	<div class="card-body p-0">
		<div class="table-responsive">
			<table class="table table-sm table-hover mb-0" id="resource-table">
				<thead class="thead-dark">
					<tr id="resource-report-head">
						@foreach($table as $key=>$value)
						<th style="min-width: 200px;" id="resource-report-head-{{ data_get($table[$key],"label",$key) }}">
							@if(@$value["label"])
							{{ @$value["label"] }}
							@else
							{{ $key }}
							@endif
						</th>
						@endforeach
					</tr>
				</thead>
				<tbody>
					@foreach($data as $row)
					<tr id="resource-body-{{ $row->id }}">
						@foreach($table_keys as $key)
						<td id="resource-report-body-{{ $row->id }}-{{ data_get($table[$key],"label",$key) }}">
							{{ $controller->getColumnIndex($table,$row,$key) }}
						</td>
						@endforeach
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>