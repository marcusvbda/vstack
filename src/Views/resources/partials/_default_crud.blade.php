<div class="flex flex-col mt-2">
    <div class="flex justify-between items-center mb-3">
        <h4 class="text-3xl mt-4 flex items-center gap-5 dark:text-neutral-200">
            @if (@$resource->icon())
                <span class="{{ $resource->icon() }}"></span>
            @endif
            {{ $resource->breadcrumbLabels()[$raw_type] }}
        </h4>
    </div>
</div>
@if ($data['page_type'] == 'Edição' && $resource->useTags())
    <resource-tags-input resource='{{ $resource->id }}' resource_code='{{ @$content->code }}'></resource-tags-input>
@endif
@if (@$raw_type == 'create')
    @if (@$resource->beforeCreateSlot())
        {!! @$resource->beforeCreateSlot() !!}
    @endif
@else
    @if (@$resource->beforeEditSlot())
        {!! @$resource->beforeEditSlot() !!}
    @endif
@endif

@if ($raw_type == 'view')
    {!! $resource->makeViewContent(
        compact('data', 'params', 'content', 'raw_type', 'resource', 'current_route', 'routes'),
    ) !!}
@endif

@if ($raw_type == 'create')
    {!! $resource->makeCreateContent(compact('data', 'params', 'raw_type', 'resource', 'current_route', 'routes')) !!}
@endif

@if ($raw_type == 'edit')
    {!! $resource->makeEditContent(
        compact('content', 'data', 'params', 'raw_type', 'resource', 'current_route', 'routes'),
    ) !!}
@endif
