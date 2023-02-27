<v-datetime class='mb-3' :disabled='{{$disabled}}' label='{{$label}}' prepend='{{$prepend}}' append='{{$append}}' append='{{$append}}' :mask='{{$mask}}' description='{{$description}}' type='{{$type}}' v-model='{{"form.$field"}}' end_placeholder='{{$end_placeholder}}' start_placeholder='{{$start_placeholder}}' placeholder='{{$placeholder}}' value_format='{{$value_format}}' format='{{$format}}' v-show='{{$visible}}' :errors='{{"errors.$field ? errors.$field : false"}}' id="resource-input-datetime-{{ $field }}" {!! $eval !!}>
    <template #prepend-slot>
        {!! @$slot_top !!}
    </template>
    <template #append-slot>
        {!! @$slot_bottom !!}
    </template>
</v-datetime>