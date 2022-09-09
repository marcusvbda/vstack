<v-tags class='mb-3'  
    :disabled='{{$disabled}}'                                                                  
    label='{{$label}}'                                                                 
    description='{{$description}}'                                                              
    v-model='{{"form.$field"}}'                                                            
    :errors='{{"errors.$field ? errors.$field : false"}}'
    id="resource-input-tags-{{ $field }}" 
    {!! $eval !!}                                  
/>