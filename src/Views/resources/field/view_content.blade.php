<v-input-view-content class='mb-3' label='{{ $label }}' description='{{ $description }}'
    :defaultValue='@json($value)' :options='@json($options)' type='{{ $type }}'
    model='{{ $model }}' format='{{ $format }}' id="resource-input-view-content-{{ $label }}"
    {!! $eval !!}>
    <template #prepend-slot>
        {!! @$slot_top !!}
    </template>
    <template #append-slot>
        {!! @$slot_bottom !!}
    </template>
</v-input-view-content>
