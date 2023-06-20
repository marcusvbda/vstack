<div class="flex flex-col mt-2">
    <div class="flex justify-between items-center mb-3">
        <h4 class="text-3xl mt-4 flex items-center gap-5">
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
@if (@!$data->id)
    @if (@$resource->beforeCreateSlot())
        {!! @$resource->beforeCreateSlot() !!}
    @endif
@else
    @if (@$resource->beforeEditSlot())
        {!! @$resource->beforeEditSlot() !!}
    @endif
@endif

<resource-crud class="mt-2" :data='@json($data)' :params='@json($params)'
    style="padding-bottom: 100px" redirect="{{ $current_route }}" :breadcrumb='@json($routes)'
    @if (@$content) :content='@json($content)' @endif raw_type='{{ $raw_type }}'
    :first_btn='@json($resource->firstCrudBtn())' :second_btn='@json($resource->secondCrudBtn())'
    :acl='@json(['can_update' => $resource->canUpdate()])' :has_befores_store='@json($resource->beforeStore([]) !== false)'
    loading_message="{{ $resource->resourceLoadingSaveMassage($raw_type) }}">
    @if (@!$data->id && !@$data['id'])
        @if (@$resource->afterCreateSlot())
            <template slot="aftercreate">
                {!! @$resource->afterCreateSlot() !!}
            </template>
        @endif
    @else
        @if (@$resource->afterEditSlot())
            <template slot="afteredit">
                {!! @$resource->afterEditSlot() !!}
            </template>
        @endif
    @endif
</resource-crud>

<div id="loading-section">
    @include('vStack::resources.loader.crud_shimmer')
</div>
