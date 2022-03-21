<v-input class='mb-3'  
    :disabled='{{$disabled}}'                                                                  
    label='{{$label}}'                                                                   
    prepend='{{$prepend}}'                                                               
    append='{{$append}}'                                                               
    append='{{$append}}' 
    :mask='{{$mask}}'                                                              
    description='{{$description}}'
	maxlength="{{ $maxlength }}"                                                              
    type='{{$type}}'                                                                     
    v-model='{{"form.$field"}}'                                                            
    placeholder='{{$placeholder}}'   
    v-show='{{$visible}}'                                                    
    :errors='{{"errors.$field ? errors.$field : false"}}'  
    id="resource-input-text-{{ $field }}"
    {!! $eval !!}                  
/>