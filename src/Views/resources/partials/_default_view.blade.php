@php
$row = $resource->getModelInstance()->findOrFail($data['id']);
@endphp
<div class="row mt-2">
    <div class="col-12">
        <div class="d-flex flex-row justify-content-between mb-3">
            <h4 class="d-flex flex-row">
                @if (@$resource->icon())
                <span class="{{ $resource->icon() }} mr-2"></span>
                @endif
                {{ $resource->breadcrumbLabels()[$raw_type] }}
            </h4>
        </div>
    </div>
</div>
@if ($resource->useTags())
<resource-tags-input resource='{{ $resource->id }}' resource_code='{{ @$content->code }}' only_view></resource-tags-input>
@endif
<div class="w-100">
    @if (@$resource->beforeViewSlot())
    {!! @$resource->beforeViewSlot() !!}
    @endif
</div>
<resource-view :data="{{ json_encode($data) }}" :params="{{ json_encode($params) }}" redirect="{{ $current_route }}" :breadcrumb="{{ json_encode($routes) }}" @if (@$content) :content="{{ json_encode($content) }}" @endif right_card_content="{{ $resource->crudRightCardBody() }}" raw_type='{{ $raw_type }}' :first_btn='@json($resource->firstViewBtn())' :second_btn='@json($resource->secondViewBtn())' :acl='@json(["can_update" => $resource->canUpdate() && $resource->canUpdateRow($row)])' :crud_type='@json($resource->crudType())' code="{{ $row->code }}">
</resource-view>

@if (@$resource->afterViewSlot())
{!! @$resource->afterViewSlot() !!}
@endif