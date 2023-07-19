<div class="mt-24">
    <div class="w-full flex flex-col items-center justify-center mt-8 mb-8">
        @if ($resource->icon())
            <h1 class="{{ $resource->icon() }} text-9xl text-neutral-500 dark:text-neutral-200"></h1>
        @endif
        <h4 class="text-3xl flex flex-col items-center justify-center dark:text-neutral-200">
            {{ $resource->noResultsFoundText() }}
            @if (@!$report_mode)
                <small class="text-sm text-neutral-500">{!! $resource->nothingStoredSubText() !!}</small>
            @endif
        </h4>
    </div>
</div>
