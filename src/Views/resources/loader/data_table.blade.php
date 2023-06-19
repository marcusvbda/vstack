@if ($data->count() <= 0)
    @include('vStack::resources.partials._no_data')
@else
    @if (!$report_mode)
        @include('vStack::resources.partials._table')
    @else
        @include('vStack::resources.partials._report_table')
    @endif
@endif
