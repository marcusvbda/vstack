<v-select v-model='{{ 'form.' . $field }}' label='{{ $label }}' :disabled='{{ $disabled }}'
    description='{{ $description }}' v-show='{{ $visible }}' placeholder='{{ $placeholder }}'
    :multiple='@json($multiple)' route_list='{{ $route_list }}' field_index="{{ $field }}"
    :option_list='{{ $options }}' :model_filter='{{ $model_filter }}' :allow_create='{{ $allow_create }}'
    :model_fields='@json($model_fields)' type={{ $type }} all_options_label="{{ $all_options_label }}"
    list_model='{{ $model }}' :errors='{{ "errors.$field ? errors.$field : false" }}'
    id="resource-input-belongsto-{{ $field }}" {!! $eval !!}>
    <template #prepend-slot>
        {!! @$slot_top !!}
    </template>
    <template #append-slot>
        {!! @$slot_bottom !!}
    </template>
</v-select>
