<v-resource-tree class='mb-3'  
    :disabled='{{$disabled}}'                             
    id="resource-input-resource-tree-{{ $resource }}" 
    :form="form"
    relation="{{ $relation }}"
    resource='{{ $resource }}'
    route_load='{{ $route_load }}'
    parent_resource="{{ $parent_resource }}"
    {!! $eval !!}                                  
/>