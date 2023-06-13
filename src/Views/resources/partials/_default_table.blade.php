<?php
$_data = request()->all();
$order_by   = Arr::get($_data, 'order_by', "id");
$current_order_type = Arr::get($_data, 'order_type', "desc");
$order_type = $current_order_type == "desc" ? "asc" : "desc";
?>
<div ref="container" class="card text-gray-700 border border-gray-200 rounded-lg bg-gray-50">
	<div class="p-1" style="min-height: 40px;">
		<div class="flex align-center flex-wrap">
			@if($resource->lenses())
			@include("vStack::resources.partials._lenses")
			@endif
		</div>
	</div>
	<div class="p-0">
		<?php
		$has_actions = count($resource->actions()) > 0;
		$show_right_actions_column = $resource->showRightActionsColumn();
		?>
		@if($has_actions)
		<?php
		$actions = array_map(function ($action) {
			return (object)[
				"id" => $action->id,
				"title" => @$action->title ? $action->title : $action->id
			];
			return $action;
		}, $resource->actions());
		?>
		<action-process resource_id="{{ $resource->id }}" :ids='@json($data->pluck("id")->toArray())' :actions='@json($actions)'></action-process>
		@endif
		<div class="table-responsive-sm bg-white">
			<table class="w-full">
				<thead id="resource-list-head">
					<tr class="border">
						<th width="1%;"></th>
						@if($has_actions)
						<th width="1%;" id="resource-list-head-action" class="p-2">
							<input type="checkbox" id="{{ $resource->id.'_action_select_all' }}" />
						</th>
						@endif
						@foreach($table as $key=>$value)
						<?php
						$size = data_get($value, 'size', 'auto');
						$col_class = data_get($value, "col_class", 'text-left');
						$sortable_index = data_get($value, "sortable_index", $key);
						if (!preg_match('/\d+(%|em|px|rem)/', $size) && is_numeric($size)) {
							$size .= 'px';
						}
						?>
						<th width="{{$size}}" class="{{ $col_class }} p-2" id="resource-list-head-{{ $sortable_index }}">
							@if(@data_get($value,"sortable") !== false)
							<a href="{{ResourcesHelpers::sortLink($resource->route(),request()->all(), $sortable_index,$order_type)}}" class="flex gap-4">
								<div class="link">{{data_get($value,"label",$value)}}</div>
								<div class="ml-auto flex  mr-4">
									<span class="sort-icon el-icon-arrow-down
													@if($order_type=='asc' && $order_by==$sortable_index ) block @else hidden @endif">
									</span>
									<span class="sort-icon el-icon-arrow-up
													@if($order_type=='desc' && $order_by==$sortable_index) block @else hidden @endif">
									</span>
								</div>
							</a>
							@else
							<div class="link-sortable">{{isset($value["label"]) ? @$value["label"] : $value}}</div>
							@endif
						</th>
						@endforeach
						@if($show_right_actions_column)
						<?php
						$qty_extra_btns = $resource->qtyShowingButtons();
						$action_size = 210 + ($qty_extra_btns * 30);
						?>
						<th style="max-width: {{$action_size}}px;width: {{$action_size}}px" id="resource-list-head-actions">
						</th>
						@endif
					</tr>
				</thead>
				<?php
				$rows_data = [];
				foreach ($data as $row) {
					$row_data = (new marcusvbda\vstack\Controllers\VstackController())->resourceTableContent($resource, null, $row, true, true);
					$rows_data[] = $row_data;
				}
				?>
				<tbody is="resource-tablelist-allinone" :rows='@json($rows_data)' :table_keys='@json($table_keys)' :has_actions='@json($has_actions)' :show_right_actions_column='@json($show_right_actions_column)' resource_id="{{$resource->id}}" resource_route="{{$resource->route()}}">
				</tbody>
			</table>
		</div>
	</div>
</div>