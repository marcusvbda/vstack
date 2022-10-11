@php
	$_data = request()->all();
	$order_by   = Arr::get($_data, 'order_by', "id");
	$current_order_type = Arr::get($_data, 'order_type', "desc");
	$order_type = $current_order_type == "desc" ? "asc" : "desc";
@endphp
<select-table-style data-aos="fade-right" id="{{$resource->id}}" :list_type="{{json_encode($list_types)}}" :has_lenses="{{json_encode(count($resource->lenses())>0)}}"
	selected_list_type="{{$list_type}}">
		<template slot="lenses">
			<div class="col-md-9 col-sm-12">
				@if($resource->lenses())
					@include("vStack::resources.partials._lenses")
				@endif
			</div>
		</template>
    <template slot="content">		
		@if($list_type == "table")
			@php
				$table_after_row = @$resource->tableAfterRow(@$data[0]) !== false;
				$has_actions = count($resource->actions()) > 0;
				$show_right_actions_column = $resource->showRightActionsColumn();
			@endphp
			@if($has_actions)
				@php
					$actions = array_map(function($action) {
						return (object)[
							"id" => $action->id,
							"title" => @$action->title ? $action->title : $action->id
					];
						return $action;
					},$resource->actions());
				@endphp
				<action-process resource_id="{{ $resource->id }}" :ids='@json($data->pluck("id")->toArray())' :actions='@json($actions)'></action-process>
			@endif
			<div class="table-responsive-sm">
				<table class="table table-striped hovered resource-table table-hover mb-0">
					<thead id="resource-list-head">
						<tr>
							@if($table_after_row)
								<th  width="1%;"></th>
							@endif
							@if($has_actions)
								<th  width="1%;" id="resource-list-head-action">
									<div class="d-flex align-items-center justify-content-center">
										<input class='select-action-resource' type="checkbox" id="{{ $resource->id.'_action_select_all' }}"  />
									</div>
								</th>
							@endif
							@foreach($table as $key=>$value)
								@php
									$size = data_get($value,'size','auto');
									$col_class = data_get($value,"col_class",'text-left');
									$sortable_index = data_get($value,"sortable_index",$key);
								@endphp
								<th width="{{$size}}" class="resource-table-col {{ $col_class }}" id="resource-list-head-{{ $sortable_index }}">					
									@if(@data_get($value,"sortable") !== false)
										<a href="{{ResourcesHelpers::sortLink($resource->route(),request()->all(), $sortable_index,$order_type)}}"
											class="d-flex flex-row align-items-center link-sortable">
											<div class="link">{{data_get($value,"label",$value)}}</div>
											<div class="ml-auto d-flex flex-row">
												<span class="sort-icon el-icon-arrow-down 
													@if($order_type=='asc' && $order_by==$sortable_index ) active @endif"
												>
												</span>
												<span class="sort-icon el-icon-arrow-up 
													@if($order_type=='desc' && $order_by==$sortable_index) active @endif"
												>
											</span>
											</div>
										</a>
									@else
										<div class="link-sortable">{{isset($value["label"]) ? @$value["label"] : $value}}</div>
									@endif
								</th>
							@endforeach
							@if($show_right_actions_column)
								@php
									$qty_extra_btns = $resource->qtyShowingButtons();
									$action_size = 210 + ($qty_extra_btns*30);
								@endphp
								<th style="max-width: {{$action_size}}px;width: {{$action_size}}px" 
									id="resource-list-head-actions"
								>
								</th>
							@endif
						</tr>
					</thead>
					@php
						$rows_data = [];
						foreach ($data as $row) {
							$row_data = (new marcusvbda\vstack\Controllers\VstackController())->resourceTableContent($resource, null, $row, true, true);
							$rows_data[] = $row_data;
						}
					@endphp	
					<tbody is="resource-tablelist-allinone" 
						:rows='@json($rows_data)'
						:table_keys='@json($table_keys)'
						:table_after_row='@json($table_after_row)'
						:has_actions='@json($has_actions)'
						:show_right_actions_column='@json($show_right_actions_column)'	
						resource_id="{{$resource->id}}"			
						resource_route="{{$resource->route()}}"			
					>
					</tbody>
				</table>
			</div>
        @else
			<div class="p-4">
				@foreach($data->chunk(3) as $chunk)
					<div class="row">
						@foreach($chunk as $row)
							<div class="col-lg-4 col-sm-12 mb-3  d-flex align-items-stretch">
								@php
									$code = \Hashids::encode($row->id);
									$crud_buttons['code'] = $code;
									$crud_buttons['route'] = $resource->route()."/".$code;
									$row_data = (new marcusvbda\vstack\Controllers\VstackController())->resourceTableContent($resource, null, $row, true, true);
								@endphp
								@include($resource->listCardView())
							</div>
						@endforeach
					</div>
				@endforeach
			</div>
        @endif
    </template>
</select-table-style>
