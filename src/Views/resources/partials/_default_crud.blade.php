@php
    $right_card_content = $resource->crudRightCardBody() ;
@endphp
<div class="row mt-2" data-aos="fade-left">
    <div class="col-12">
        <div class="d-flex flex-row justify-content-between align-items-center mb-3">
            <h4>
                @if (@$resource->icon())
                    <span class="{{ $resource->icon() }} mr-2"></span>
                @endif
                {{ $resource->breadcrumbLabels()[$raw_type] }}
            </h4>
            @if(!$right_card_content)
                <div class="top-save">
                    <portal-target  name="crud-btns">
                        {{--  --}}
                    </portal-target>
                </div>
            @endif
        </div>
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

<resource-crud :data="{{ json_encode($data) }}" :params="{{ json_encode($params) }}" redirect="{{ $current_route }}" data-aos="fade-right"
    :breadcrumb="{{ json_encode($routes) }}" @if (@$content)
    :content="{{ json_encode($content) }}"
    @endif
    :show_crud_right_card='@json($resource->showCrudRightCard())'
    right_card_content="{{ $right_card_content }}"
    raw_type='{{ $raw_type }}'
    :first_btn='@json($resource->firstCrudBtn())'
    :second_btn='@json($resource->secondCrudBtn())'
    :acl='@json(["can_update" => $resource->canUpdate()])'
    :crud_type='@json($resource->crudType())'
    :has_befores_store='@json($resource->beforeStore([]) !== false)'
    loading_message="{{$resource->resourceLoadingSaveMassage($raw_type)}}"
    >
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
    @include("vStack::resources.loader.crud_shimmer")    
</div>