<v-tags class='mb-3'  
    :disabled='{{$disabled}}'                                                                  
    label='{{$label}}'                                                                 
    description='{{$description}}'                                                              
    v-model='{{"form.$field"}}'                                                            
    :errors='{{"errors.$field ? errors.$field : false"}}'
    id="resource-input-tags-{{ $field }}" 
    {!! $eval !!}                                  
>
<template #prepend-slot>
    {!! $slot_top !!}
</template>
<template #append-slot>
    {!! $slot_bottom !!}
</template>
</v-tags>