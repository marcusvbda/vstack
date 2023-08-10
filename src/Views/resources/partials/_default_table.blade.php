@php
    $_data = request()->all();
    $order_by = Arr::get($_data, 'order_by', 'id');
    $current_order_type = Arr::get($_data, 'order_type', 'desc');
    $order_type = $current_order_type == 'desc' ? 'asc' : 'desc';
    $can_interact = in_array($raw_type, ['edit', 'list']);
@endphp
@if (@$list_type == 'full')
    <vstack-cursor-pages class="flex justify-end my-4 flex-wrap" :appends='@json($_data)'
        previous_cursor="{{ $previous_cursor }}" next_cursor="{{ $next_cursor }}">
    </vstack-cursor-pages>
@endif
<vstack-resource-loading>
    <div ref="container"
        class="text-gray-700 border dark:border-none border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-700"
        @if (@$list_type == 'full') style="margin-bottom: 200px" @endif>
        <div class="p-1" style="min-height: 40px;">
            <div class="flex align-center flex-wrap">
                @if ($resource->lenses())
                    @include('vStack::resources.partials._lenses')
                @endif
            </div>
        </div>
        <div class="p-0">
            @php
                $show_right_actions_column = $can_interact && $resource->showRightActionsColumn();
            @endphp
            @if (@$list_type == 'full')
                @php
                    $has_actions = count($resource->actions()) > 0;
                @endphp
                @if ($has_actions)
                    @php
                        $actions = array_map(function ($action) {
                            return (object) [
                                'id' => $action->id,
                                'title' => @$action->title ? $action->title : $action->id,
                            ];
                            return $action;
                        }, $resource->actions());
                    @endphp
                    <action-process resource_id="{{ $resource->id }}" :ids='@json($data->pluck('id')->toArray())'
                        :actions='@json($actions)'></action-process>
                @endif
            @endif
            <div class="table-responsive-sm bg-white">
                <table class="w-full vstack-resource-list">
                    <thead id="resource-list-head">
                        <tr class="border dark:border-none">
                            <th width="1%;" class="dark:bg-gray-800"></th>
                            @if (@$list_type == 'full' && @$has_actions)
                                <th width="1%;" id="resource-list-head-action" class="p-2 dark:bg-gray-800">
                                    <input type="checkbox" id="{{ $resource->id . '_action_select_all' }}" />
                                </th>
                            @endif
                            @foreach ($table as $key => $value)
                                @php
                                    $size = data_get($value, 'size', 'auto');
                                    $col_class = data_get($value, 'col_class', 'text-left');
                                    $sortable_index = data_get($value, 'sortable_index', $key);
                                    if (!preg_match('/\d+(%|em|px|rem)/', $size) && is_numeric($size)) {
                                        $size .= 'px';
                                    }
                                @endphp
                                <th width="{{ $size }}" class="{{ $col_class }} p-2 dark:bg-gray-800"
                                    id="resource-list-head-{{ $sortable_index }}">
                                    @if ($can_interact && @$list_type == 'full' && @data_get($value, 'sortable') !== false)
                                        <a href="{{ ResourcesHelpers::sortLink($resource->route(), request()->all(), $sortable_index, $order_type) }}"
                                            class="flex gap-4 vstack-link">
                                            <div class="link">{{ data_get($value, 'label', $value) }}</div>
                                            <div class="ml-auto flex  mr-4">
                                                <span
                                                    class="sort-icon el-icon-arrow-down
													@if ($order_type == 'asc' && $order_by == $sortable_index) block @else hidden @endif">
                                                </span>
                                                <span
                                                    class="sort-icon el-icon-arrow-up
													@if ($order_type == 'desc' && $order_by == $sortable_index) block @else hidden @endif">
                                                </span>
                                            </div>
                                        </a>
                                    @else
                                        <div class="link-sortable dark:text-neutral-200">
                                            {{ isset($value['label']) ? @$value['label'] : $value }}
                                        </div>
                                    @endif
                                </th>
                            @endforeach
                            @if ($show_right_actions_column)
                                @php
                                    $qty_extra_btns = $resource->qtyShowingButtons();
                                    $action_size = 210 + $qty_extra_btns * 30;
                                @endphp
                                <th style="max-width: {{ $action_size }}px;width: {{ $action_size }}px"
                                    class="dark:bg-gray-800" id="resource-list-head-actions">
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
                    <tbody hash="{{ $hash }}" :can_interact='@json($can_interact)'
                        is="resource-tablelist-allinone" :rows='@json($rows_data)'
                        :table_keys='@json($table_keys)' :has_actions='@json(@$list_type == 'full' && @$has_actions)'
                        :show_right_actions_column='@json($show_right_actions_column)' resource_id="{{ $resource->id }}"
                        resource_route="{{ $resource->route() }}">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</vstack-resource-loading>
@if (@$list_type !== 'full')
    <vstack-cursor-pages @if (!@$related_resource) style="margin-bottom: 200px" @endif
        class="flex justify-end my-4 flex-wrap" :appends='@json($_data)'
        previous_cursor="{{ $previous_cursor }}" next_cursor="{{ $next_cursor }}">
    </vstack-cursor-pages>
@endif
