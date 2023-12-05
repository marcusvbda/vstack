<v-select v-model='{{ 'form.' . $field }}' label='{{ $label }}' :disabled='{{ $disabled }}'
    group_by='{{ $group_by }}' description='{{ $description }}' v-show='{{ $visible }}'
    placeholder='{{ $placeholder }}' :option_template='@json($option_template)'
    :multiple='@json($multiple)' route_list='{{ $route_list }}' field_index="{{ $field }}"
    :option_list='{{ $options }}' :model_filter='{{ $model_filter }}' :allow_create='{{ $allow_create }}'
    entity_parent='{{ $entity_parent }}' entity_parent_message='{{ $entity_parent_message }}'
    :model_fields='@json($model_fields)' type={{ $type }} all_options_label="{{ $all_options_label }}"
    list_model='{{ $model }}' :errors='{{ "errors.$field ? errors.$field : false" }}'
    id="resource-input-belongsto-{{ $field }}" page_type="{{ request()?->page_type }}" {!! $eval !!}>
    <template #prepend-slot>
        {!! @$slot_top !!}
    </template>
    <template #append-slot>
        {!! @$slot_bottom !!}
    </template>
</v-select>
