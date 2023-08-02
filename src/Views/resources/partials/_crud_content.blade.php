<resource-crud class="mt-2" :data='@json($data)' :params='@json($params)'
    style="padding-bottom: 100px" redirect="{{ $current_route }}" :breadcrumb='@json($routes)'
    @if (@$content) :content='@json($content)' @endif raw_type='{{ $raw_type }}'
    :first_btn='@json($resource->firstCrudBtn())' :second_btn='@json($resource->secondCrudBtn())'
    :acl='@json(['can_update' => $resource->canUpdate()])' :has_befores_store='@json($resource->beforeStore([]) !== false)'
    loading_message="{{ $resource->resourceLoadingSaveMassage($raw_type) }}">
    @if (@$raw_type == 'create')
        @if (@$resource->afterCreateSlot())
            <template slot="aftercreate">
                {!! @$resource->afterCreateSlot() !!}
            </template>
        @endif
    @elseif(@$raw_type == 'edit')
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

