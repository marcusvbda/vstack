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
						$action->inputs = $action->inputs();
						return $action;
					},$resource->actions());
				@endphp
				<action-process resource_id="{{ $resource->id }}" :ids='@json($data->pluck("id")->toArray())' :actions='@json($actions)'></action-process>
			@endif
			<div class="table-responsive-sm">
				<table class="table table-striped hovered resource-table table-hover mb-0">
					<thead>
						<tr>
							@if($table_after_row)
								<th  width="1%;"></th>
							@endif
							@if($has_actions)
								<th  width="1%;">
									<div class="d-flex align-items-center justify-content-center">
										<input class='select-action-resource' type="checkbox" id="{{ $resource->id.'_action_select_all' }}"  />
									</div>
								</th>
							@endif
							@foreach($table as $key=>$value)
							@php
								$size =  (isset($value['size']) ? $value['size'] : 'auto');
								$col_class = (isset($value['col_class']) ? $value['col_class'] : 'text-left');
								$sortable_index = isset($value['sortable_index']) ? $value['sortable_index'] : $key;
							@endphp
							<th  width="{{$size}}" class="resource-table-col {{ $col_class }}">
								@if(@$value["sortable"]!==false)
									<a href="{{ResourcesHelpers::sortLink($resource->route(),request()->query(), $sortable_index,$order_type)}}"
										class="d-flex flex-row align-items-center link-sortable">
										<div class="link">{{isset($value["label"]) ? @$value["label"] : $value}}</div>
										<div class="ml-auto d-flex flex-row">
											<span
												class="sort-icon el-icon-arrow-down @if($order_type=='asc' && $order_by==$sortable_index ) active @endif"></span>
											<span
												class="sort-icon el-icon-arrow-up @if($order_type=='desc' && $order_by==$sortable_index) active @endif"></span>
										</div>
									</a>
								@else
									<div class="link-sortable">{{isset($value["label"]) ? @$value["label"] : $value}}</div>
								@endif
							</th>
							@endforeach
							@if($show_right_actions_column)
								<th  style="max-width: 210px;width: 210px;"></th>
							@endif
						</tr>
					</thead>
					@php
						$load_list_item_by_item = $resource->loadListItemByItem();
					@endphp
					@if($load_list_item_by_item)
						<tbody>
							@foreach($data as $row)
								@php 
									$code = \Hashids::encode($row->id);
									$columns_count = count($resource->table())+($has_actions ? 1 : 0)+($table_after_row ? 2 : 0);
								@endphp
								<tr is="get-resource-content" :cols={{count($table_keys)}} row_code="{{$code}}" :raw_content='@json($row)'
									resource_route="{{$resource->route()}}" resource_id="{{$resource->id}}" row_id="{{$row->id}}"
									:show_right_actions_column='@json($show_right_actions_column)'
									type="resourceTableContent"
									:has_actions='@json($has_actions)'
									>
									@if($table_after_row)
										<template slot="first-column">
											<td  style="width:1%;padding-bottom: 10px!important;height: 1px;">
												<portal-target class="h-100 d-flex justify-content-center" name="resource_after_row_arrow_{{ $row->id }}"></portal-target>
											</td>
										</template>
									@endif
									@if($has_actions)
										<template slot="first-column">
											<td  width="1%;" >
												<div class="d-flex align-items-center justify-content-center">
													<input class="select-action-resource select_action_box" type="checkbox" id="{{ $resource->id.'_action_select_'.$row->id }}" />
												</div>
											</td>
										</template>
									@endif
								</tr>
								@if($table_after_row)
									<tr class="table-row after">
										<td  colspan="{{ $columns_count }}" >
											<after-row-resource row_id="{{  $row->id }}">
												{!! $resource->tableAfterRow($row) !!}
											</after-row-resource>
										</td>
									</tr>
								@endif
							@endforeach
						</tbody>
					@else
						@php
							$item_ids = $data->pluck("id")->toArray();
						@endphp
						<tbody is="resource-tablelist-allinone" 
							:ids='@json($item_ids)'
							:table_keys='@json($table_keys)'
							:table_after_row='@json($table_after_row)'
							:has_actions='@json($has_actions)'
							:show_right_actions_column='@json($show_right_actions_column)'	
							resource_id="{{$resource->id}}"			
							resource_route="{{$resource->route()}}"			
						>
						</tbody>
					@endif
				</table>
			</div>
        @else
			<div class="p-4">
				@foreach($data->chunk(3) as $chunk)
				<div class="row">
					@foreach($chunk as $row)
					<div class="col-lg-4 col-sm-12 mb-3  d-flex align-items-stretch">
						<?php
							$code = \Hashids::encode($row->id);
							$crud_buttons['code'] = $code;
							$crud_buttons['route'] = $resource->route()."/".$code;
						?>
						@include($resource->listCardView())
					</div>
					@endforeach
				</div>
				@endforeach
			</div>
        @endif
    </template>
</select-table-style>
