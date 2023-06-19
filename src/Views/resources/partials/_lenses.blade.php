<div class="flex  p-2 gap-4 items-center justify-center flex-wrap" id="resource-lenses">
    @php
        $current_len = @$_data['current_len'] ? @$_data['current_len'] : 'all';
        $lenses = $resource->lenses();
        $query = request()->except(['cursor']);
    @endphp
    @if ($current_len == 'all')
        <b class="text-neutral-700 text-sm">Todos</b>
    @else
        @php
            foreach ($lenses as $len_key => $len_value) {
                if (isset($query[$len_value['field']])) {
                    unset($query[$len_value['field']]);
                }
            }
            
            if (isset($query['current_len'])) {
                unset($query['current_len']);
            }
            $route = route('resource.index', array_merge(['resource' => $resource->id], $query));
        @endphp
        <a href="{{ $route }}">Todos</a>
    @endif
    @foreach ($lenses as $len_key => $len_value)
        @php
            $field = $len_value['field'];
            $other_fields = array_filter($lenses, function ($row) use ($field) {
                return $row['field'] != $field;
            });
            $query[$len_value['field']] = $len_value['value'];
            $query['current_len'] = $len_key;
            foreach ($other_fields as $other) {
                if (@$query[$other['field']]) {
                    unset($query[$other['field']]);
                }
            }
            $route = route('resource.index', array_merge(['resource' => $resource->id], $query));
        @endphp
        <div class="px-2">|</div>
        @if ($current_len == $len_key)
            <b id="resource-lenses-{{ $len_key }}" class="text-neutral-700 text-sm">{!! $len_key !!}</b>
        @else
            <a id="resource-lenses-{{ $len_key }}" href="{{ $route }}" class="text-neutral-600 text-sm">
                {!! $len_key !!}
            </a>
        @endif
    @endforeach
</div>
