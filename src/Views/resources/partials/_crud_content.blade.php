@php
    $relatedResources = $resource->relatedResources();
    $hasRelatedResources = count($relatedResources) > 0;
    $hash = request()?->hash ? request()->hash : '';
@endphp
<resource-crud class="mt-2" :data='@json($data)' :params='@json($params)'
    hash="{{ $hash }}" @if (!$hasRelatedResources) style="padding-bottom: 100px" @endif
    redirect="{{ $current_route }}" :breadcrumb='@json($routes)'
    @if (@$content) :content='@json($content)' @endif raw_type='{{ $raw_type }}'
    :first_btn='@json($resource->firstCrudBtn())' :second_btn='@json($resource->secondCrudBtn())'
    :acl='@json(['can_update' => $resource->canUpdate()])' :has_befores_store='@json($resource->beforeStore([]) !== false)'
    resource_id="{{ $resource->id }}" loading_message="{{ $resource->resourceLoadingSaveMassage($raw_type) }}">
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

@if ($hasRelatedResources)
    <div style="padding-bottom: 100px">
        @foreach ($relatedResources as $relatedResource)
            <resource-crud-card mh="400px" label="{{ $relatedResource->label() }}">
                <div class="p-4">
                    {!! $relatedResource->makeIndexContent([
                        'resource' => $relatedResource,
                        'report_mode' => false,
                        'only_table' => true,
                        'related_resource' => $resource->id,
                        'related_resource_id' => $content->id,
                        'extra_filters' => [],
                        'raw_type' => $raw_type,
                    ]) !!}
                </div>
            </resource-crud-card>
        @endforeach
    </div>
@endif

<div id="loading-section">
    @include('vStack::resources.loader.crud_shimmer')
</div>
