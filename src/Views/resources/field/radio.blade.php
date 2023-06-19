<v-radio v-model='{{ "form.$field" }}' label='{{ $label }}' :disabled='{{ $disabled }}'
    description='{{ $description }}' :option_list='{{ $options }}'
    :errors='{{ "errors.$field ? errors.$field : false" }}' id="resource-radio-morphstomany-{{ $field }}"
    {!! $eval !!}>
    <template #prepend-slot>
        {!! @$slot_top !!}
    </template>
    <template #append-slot>
        {!! @$slot_bottom !!}
    </template>
</v-radio>
