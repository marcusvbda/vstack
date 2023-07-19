<vstack-cursor-pages class="flex justify-end my-4 flex-wrap" :appends='@json($_data)'
    previous_cursor="{{ $previous_cursor }}" next_cursor="{{ $next_cursor }}">
</vstack-cursor-pages>
<vstack-resource-loading>
    <div class="overflow-x-auto bg-white" style="margin-bottom: 200px">
        <table class="mb-0 vstack-resource-list w-full" id="resource-table">
            <thead class="thead-dark">
                <tr id="resource-report-head">
                    @foreach ($table as $key => $value)
                        <th style="min-width: 200px;text-align:left;"
                            class="bg-gray-600 text-white text-sm p-1 border dark:border-none dark:bg-gray-800 dark:text-neutral-200"
                            id="resource-report-head-{{ data_get($table[$key], 'label', $key) }}">
                            @if (@$value['label'])
                                {{ @$value['label'] }}
                            @else
                                {{ $key }}
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $row)
                    <tr id="resource-body-{{ $row->id }}">
                        @foreach ($table_keys as $key)
                            <td id="resource-report-body-{{ $row->id }}-{{ data_get($table[$key], 'label', $key) }}"
                                class="text-xs text-neutral-700 border p-1 dark:border-none dark:bg-gray-700 dark:text-neutral-300">
                                {{ $controller->getColumnIndex($table, $row, $key) }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</vstack-resource-loading>
