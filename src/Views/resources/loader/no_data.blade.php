<div class="d-flex flex-column row align-items-center justify-items-center">
    <div class="col-md-6 col-sm-12 text-center">
        <h4 class="text-center mt-5">
            @if ($resource->icon())
                <h1 style="opacity: .3;font-size: 250px;"><span class="{{ $resource->icon() }}"></span></h1>
            @endif
            <div>{!! $resource->nothingStoredText() !!}</div>
            @if (@!$report_mode)
                @if ($resource->canCreate() || $resource->canImport())
                    <div>{!! $resource->nothingStoredSubText() !!}</div>
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
