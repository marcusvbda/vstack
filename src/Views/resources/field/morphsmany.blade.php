<v-tags multiple 
    :allowcreate='true' 
    :required='{{$required}}' 
    v-model='{{"form.$field"}}' 
    label='{{$label}}' 
    :disabled='{{$disabled}}' 
    :unique='{{$unique}}'                    
    placeholder='{{$placeholder}}' 
    :errors='{{"errors.$field ? errors.$field : false"}}' 
    id="resource-input-morphstomany-{{ $field }}" 
    {!! $eval !!}      
>
<template #prepend-slot>
    {!! $slot_top !!}
</template>
<template #append-slot>
    {!! $slot_bottom !!}
</template>
</v-tags>