<v-tiny class='mb-3' :disabled='{{$disabled}}' label='{{$label}}' :height='{{$height}}' :show_value_length='{{$show_value_length}}' description='{{$description}}' maxlength="{{ $maxlength }}" v-model='{{"form.$field"}}' placeholder='{{$placeholder}}' v-show='{{$visible}}' :errors='{{"errors.$field ? errors.$field : false"}}' id="resource-input-text-{{ $field }}" {!! $eval !!}>
    <template #prepend-slot>
        {!! @$slot_top !!}
    </template>
    <template #append-slot>
        {!! @$slot_bottom !!}
    </template>
</v-tiny>