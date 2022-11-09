<v-input-textarea class='mb-3'  
    :disabled='{{$disabled}}'                                                                  
    label='{{$label}}'                                                                     
    type='{{$type}}'           
    :rows='{{$rows}}'                                                          
    v-model='{{"form.$field"}}'    
    :show_value_length='{{$show_value_length}}'                                                      
    placeholder='{{$placeholder}}'                                                       
    description='{{$description}}'                                                       
    :errors='{{"errors.$field ? errors.$field : false"}}'  
	maxlength="{{ $maxlength }}"   
    id="resource-input-textarea-{{ $field }}" 
    {!! $eval !!}              
>
<template #prepend-slot>
    {!! $slot_top !!}
</template>
<template #append-slot>
    {!! $slot_bottom !!}
</template>
</v-input-textarea>