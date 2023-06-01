<div class="d-flex flex-column row align-items-center justify-items-center">
    <div class="col-md-6 col-sm-12 text-center">
        <h4 class="text-center my-4 d-flex flex-column">
            @if ($resource->icon())
                <h1 style="opacity: .3;font-size: 200px;"><span class="{{ $resource->icon() }}"></span></h1>
            @endif
            {{ $resource->noResultsFoundText() }}
            @if (@!$report_mode)
                @if ($resource->canCreate() || $resource->canImport())
                    <small>{!! $resource->nothingStoredSubText() !!}</small>
                @endif
            @endif
        </h4>
        @if (!@$report_mode)
            @if ($resource->canCreate())
                <resource-store-btn big class="mt-3" resource_id='{{ $resource->id }}' :crud_type='@json($resource->crudType())'
                    label="{{ $resource->storeButtonLabel() }}" route="{{ route('resource.create', ['resource' => $resource->id]) }}">
                </resource-store-btn>
            @endif
        @endif
    </div>
</div>
