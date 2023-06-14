<v-resource-tree class='mb-3' :disabled='{{ $disabled }}' id="resource-input-resource-tree-{{ $resource }}"
    :form="form" relation="{{ $relation }}" resource='{{ $resource }}'
    parent_resource="{{ $parent_resource }}" {!! $eval !!}>
    <template #prepend-slot>
        {!! @$slot_top !!}
    </template>
    <template #append-slot>
        {!! @$slot_bottom !!}
    </template>
</v-resource-tree>
