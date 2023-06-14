<resource-list-items resource_id="{{ $resource->id }}" :request_data='@json(request()->all())'>
</resource-list-items>

@if ($data->count() <= 0)
    @include('vStack::resources.partials._no_data')
@else
    @if (!$report_mode)
        @include('vStack::resources.partials._table')
    @else
        @include('vStack::resources.partials._report_table')
    @endif
@endif
