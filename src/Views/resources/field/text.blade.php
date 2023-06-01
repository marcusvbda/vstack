<v-input class='mb-3' :disabled='{{$disabled}}' label='{{$label}}' prepend='{{$prepend}}' append='{{$append}}' append='{{$append}}' :mask='{{$mask}}' :show_value_length='{{$show_value_length}}' description='{{$description}}' min="{{ $min }}" maxlength="{{ $maxlength }}" type='{{$type}}' v-model='{{"form.$field"}}' placeholder='{{$placeholder}}' :step='{{$step}}' v-show='{{$visible}}' :errors='{{"errors.$field ? errors.$field : false"}}' id="resource-input-text-{{ $field }}" {!! $eval !!}>
    <template #prepend-slot>
        {!! @$slot_top !!}
    </template>
    <template #append-slot>
        {!! @$slot_bottom !!}
    </template>
</v-input>