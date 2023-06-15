<div class="overflow-x-auto bg-white">
    <table class="table table-sm table-hover mb-0 vstack-resource-list" id="resource-table">
        <thead class="thead-dark">
            <tr id="resource-report-head">
                @foreach ($table as $key => $value)
                    <th style="min-width: 200px;text-align:left;" class="bg-gray-600 text-white text-sm p-1 border"
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
                            class="font-sm text-neutral-700 border p-1">
                            {{ $controller->getColumnIndex($table, $row, $key) }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
